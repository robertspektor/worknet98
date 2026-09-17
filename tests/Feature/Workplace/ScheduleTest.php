<?php

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Technician;
use App\Models\User;

beforeEach(fn () => $this->travelTo('2026-09-18 09:00:00'));

it('shows the next five working days with technicians and their busy slots', function () {
    $branch = Branch::factory()->create();
    Technician::factory()->for($branch)->create([
        'name' => 'Rita Vance',
        'skills' => ['plumbing', 'heating'],
        'busy_slots' => [['weekday' => 1, 'slot' => '08:00']],
    ]);
    $player = playerOnDutyAt($branch);

    $this->actingAs($player)
        ->getJson(route('api.v1.schedule.show'))
        ->assertOk()
        ->assertJsonPath('data.days', ['2026-09-21', '2026-09-22', '2026-09-23', '2026-09-24', '2026-09-25'])
        ->assertJsonPath('data.slots', ['08:00', '10:00', '13:00', '15:00'])
        ->assertJsonPath('data.technicians.0.name', 'Rita Vance')
        ->assertJsonPath('data.technicians.0.skills', ['plumbing', 'heating'])
        ->assertJsonPath('data.technicians.0.busy', ['2026-09-21 08:00']);
});

it('shows the appointments of the whole branch and who booked them', function () {
    $branch = Branch::factory()->create();
    $player = playerOnDutyAt($branch);
    $colleague = playerOnDutyAt($branch);
    $colleague->employment?->position->update(['title' => 'Office Assistant (Scheduling)']);
    Appointment::factory()->for($branch)->create(['booked_by_employment_id' => $player->employment?->id, 'date' => '2026-09-22', 'slot' => '10:00']);
    Appointment::factory()->for($branch)->create(['booked_by_employment_id' => $colleague->employment?->id, 'date' => '2026-09-22', 'slot' => '13:00']);
    Appointment::factory()->create(['date' => '2026-09-22']);

    $this->actingAs($player)
        ->getJson(route('api.v1.schedule.show'))
        ->assertJsonCount(2, 'data.appointments')
        ->assertJsonPath('data.appointments.*.slot', ['10:00', '13:00'])
        ->assertJsonPath('data.appointments.*.is_own', [true, false])
        ->assertJsonPath('data.appointments.1.booked_by', 'Office Assistant (Scheduling)');
});

it('requires an employment for company software', function () {
    $this->actingAs(User::factory()->create())
        ->getJson(route('api.v1.schedule.show'))
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'not_employed');
});

it('names the company software of the employer', function () {
    $company = Company::factory()->create(['slug' => 'flowright-plumbing']);
    $branch = Branch::factory()->for($company)->create(['name' => 'Maple Falls', 'office_address' => 'office@flowright.wn']);

    $this->actingAs(playerOnDutyAt($branch))
        ->getJson(route('api.v1.company-software.show'))
        ->assertOk()
        ->assertJsonPath('data.branch', 'Maple Falls')
        ->assertJsonPath('data.office_address', 'office@flowright.wn')
        ->assertJsonPath('data.app_names', ['records' => 'CustomerBase', 'scheduler' => 'ServicePlan']);
});
