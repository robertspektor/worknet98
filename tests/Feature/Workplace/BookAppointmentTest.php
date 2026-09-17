<?php

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Shift;
use App\Models\Technician;
use App\Models\User;
use App\Workplace\Events\AppointmentBooked;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;

beforeEach(function () {
    $this->travelTo('2026-09-18 09:00:00');
    $this->branch = Branch::factory()->create();
    $this->customer = Customer::factory()->for($this->branch)->create();
    $this->technician = Technician::factory()->for($this->branch)->create([
        'busy_slots' => [['weekday' => 1, 'slot' => '08:00']],
    ]);
    $this->player = playerOnDutyAt($this->branch);
});

function book(User $player, array $overrides = []): TestResponse
{
    return test()->actingAs($player)->postJson(route('api.v1.appointments.store'), [
        'customer_id' => test()->customer->id,
        'technician_id' => test()->technician->id,
        'date' => '2026-09-21',
        'slot' => '10:00',
        ...$overrides,
    ]);
}

it('books an appointment for a customer with a technician', function () {
    Event::fake([AppointmentBooked::class]);

    book($this->player)
        ->assertCreated()
        ->assertJsonPath('data.date', '2026-09-21')
        ->assertJsonPath('data.slot', '10:00')
        ->assertJsonPath('data.customer.name', $this->customer->person->name);

    $appointment = Appointment::sole();
    expect($appointment->booked_by_employment_id)->toBe($this->player->employment?->id)
        ->and($appointment->branch_id)->toBe($this->branch->id)
        ->and($appointment->technician_id)->toBe($this->technician->id);
    Event::assertDispatched(AppointmentBooked::class);
});

it('refuses bookings while the player is not on duty', function () {
    Shift::query()->update(['clocked_out_at' => now()]);

    book($this->player)->assertUnprocessable()->assertJsonPath('refusal', 'not_on_duty');
});

it('refuses slots in which the technician is busy', function () {
    book($this->player, ['slot' => '08:00'])->assertUnprocessable()->assertJsonPath('refusal', 'technician_busy');
});

it('refuses slots that are already booked', function () {
    book($this->player)->assertCreated();

    book($this->player)->assertUnprocessable()->assertJsonPath('refusal', 'slot_taken');
});

it('refuses slots that a colleague in the same branch already booked', function () {
    book(playerOnDutyAt($this->branch))->assertCreated();

    book($this->player)->assertUnprocessable()->assertJsonPath('refusal', 'slot_taken');
});

it('refuses dates outside of the next five working days', function (string $date) {
    book($this->player, ['date' => $date])->assertUnprocessable()->assertJsonPath('refusal', 'outside_booking_window');
})->with(['today' => '2026-09-18', 'weekend' => '2026-09-19', 'too far ahead' => '2026-09-28']);

it('only books customers and technicians of the own branch', function () {
    $otherBranch = Branch::factory()->for($this->branch->company)->create();

    book($this->player, [
        'customer_id' => Customer::factory()->for($otherBranch)->create()->id,
        'technician_id' => Technician::factory()->for($otherBranch)->create()->id,
        'slot' => '11:00',
    ])->assertUnprocessable()->assertJsonValidationErrors(['customer_id', 'technician_id', 'slot']);
});

it('cancels an own appointment', function () {
    $appointment = Appointment::factory()->for($this->branch)->create(['booked_by_employment_id' => $this->player->employment?->id]);

    $this->actingAs($this->player)
        ->deleteJson(route('api.v1.appointments.destroy', $appointment))
        ->assertNoContent();

    expect(Appointment::count())->toBe(0);
});

it('does not cancel appointments of colleagues', function () {
    $colleague = playerOnDutyAt($this->branch);
    $appointment = Appointment::factory()->for($this->branch)->create(['booked_by_employment_id' => $colleague->employment?->id]);

    $this->actingAs($this->player)
        ->deleteJson(route('api.v1.appointments.destroy', $appointment))
        ->assertForbidden();
});

it('does not cancel a visit that already failed', function () {
    $appointment = Appointment::factory()->for($this->branch)->create([
        'booked_by_employment_id' => $this->player->employment?->id,
        'executed_at' => now(),
        'failed_at' => now(),
    ]);

    $this->actingAs($this->player)
        ->deleteJson(route('api.v1.appointments.destroy', $appointment))
        ->assertForbidden();

    expect(Appointment::count())->toBe(1);
});
