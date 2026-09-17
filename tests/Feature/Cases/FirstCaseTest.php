<?php

use App\Cases\Metric;
use App\Cases\MetricBook;
use App\Cases\WorkCaseStatus;
use App\Models\Customer;
use App\Models\Email;
use App\Models\User;
use App\Models\WorkCase;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CompanySeeder;

beforeEach(function () {
    $this->travelTo('2026-09-21 09:00:00');
    $this->seed([CompanySeeder::class, BranchSeeder::class]);
    $employment = employAtSeededPosition('flowright-plumbing');
    $this->employment = $employment;
    $this->player = User::findOrFail($employment->user_id);
    $this->hollis = Customer::query()->where('slug', 'margaret-hollis')->sole();
});

function handleHollisCase(User $player, Customer $hollis, string $technician, string $date, string $slot): void
{
    workAs($player, 'POST', 'api.v1.appointments.store', ['customer_id' => $hollis->id, 'technician_id' => technician($technician)->id, 'date' => $date, 'slot' => $slot]);
    workAs($player, 'POST', 'api.v1.emails.store', ['customer_id' => $hollis->id, 'subject' => 'Appointment', 'body' => 'See you then.', 'action' => 'confirm_appointment']);
    workAs($player, 'POST', 'api.v1.calendar-entries.store', ['date' => $date, 'time' => $slot, 'title' => 'Hollis']);
}

it('delivers the customer request when the first shift starts', function () {
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');

    $request = Email::sole();
    expect($request->employment_id)->toBe($this->employment->id)
        ->and($request->sender_name)->toBe('Margaret Hollis')
        ->and($request->subject)->toBe('Water under my kitchen sink!!')
        ->and(WorkCase::sole()->status)->toBe(WorkCaseStatus::Open);
});

it('resolves the case with praise when it was planned well', function () {
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');
    handleHollisCase($this->player, $this->hollis, 'rita-vance', '2026-09-22', '10:00');
    workAs($this->player, 'POST', 'api.v1.shift.clock-out');

    carryOutAppointmentsAt('2026-09-22 12:00:00');

    $metrics = app(MetricBook::class);
    expect(WorkCase::sole()->status)->toBe(WorkCaseStatus::Resolved)
        ->and($metrics->valueOf($this->employment, Metric::CustomerSatisfaction))->toBe(2)
        ->and($metrics->valueOf($this->employment, Metric::Cost))->toBe(0)
        ->and($metrics->valueOf($this->employment, Metric::Punctuality))->toBe(1);

    $feedback = Email::query()->where('subject', 'Re: Mrs. Hollis')->sole();
    expect($feedback->sender_name)->toBe('Gary Flowright')
        ->and($feedback->body)->toContain('the morning appointment is perfect')
        ->and($feedback->body)->toContain('Good pick on the technician')
        ->and($feedback->body)->toContain('And fast');
});

it('lowers the company metrics when it was planned badly', function () {
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');
    workAs($this->player, 'POST', 'api.v1.appointments.store', ['customer_id' => $this->hollis->id, 'technician_id' => technician('rita-vance')->id, 'date' => '2026-09-25', 'slot' => '13:00']);
    workAs($this->player, 'POST', 'api.v1.emails.store', ['customer_id' => $this->hollis->id, 'subject' => 'Appointment', 'body' => 'See you.', 'action' => 'confirm_appointment']);
    workAs($this->player, 'POST', 'api.v1.calendar-entries.store', ['date' => '2026-09-25', 'time' => '13:00', 'title' => 'Hollis']);
    workAs($this->player, 'POST', 'api.v1.shift.clock-out');

    carryOutAppointmentsAt('2026-09-25 15:00:00');

    $metrics = app(MetricBook::class);
    expect(WorkCase::sole()->status)->toBe(WorkCaseStatus::Resolved)
        ->and($metrics->valueOf($this->employment, Metric::CustomerSatisfaction))->toBe(-2)
        ->and($metrics->valueOf($this->employment, Metric::Punctuality))->toBe(-1);
    expect(Email::query()->where('subject', 'Re: Mrs. Hollis')->sole()->body)->toContain('She volunteers at the library');
});

it('charges extra cost when the wrong technician is sent', function () {
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');
    handleHollisCase($this->player, $this->hollis, 'doug-pruitt', '2026-09-22', '08:00');
    workAs($this->player, 'POST', 'api.v1.shift.clock-out');

    carryOutAppointmentsAt('2026-09-22 10:00:00');

    expect(app(MetricBook::class)->valueOf($this->employment, Metric::Cost))->toBe(-2)
        ->and(Email::query()->where('subject', 'Re: Mrs. Hollis')->sole()->body)->toContain('You sent a heating guy');
});

it('upsets the customer when Stan is sent despite the warning', function () {
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');
    handleHollisCase($this->player, $this->hollis, 'stan-kowalski', '2026-09-24', '08:00');
    workAs($this->player, 'POST', 'api.v1.shift.clock-out');

    carryOutAppointmentsAt('2026-09-24 10:00:00');

    expect(app(MetricBook::class)->valueOf($this->employment, Metric::CustomerSatisfaction))->toBe(0)
        ->and(Email::query()->where('subject', 'Re: Mrs. Hollis')->sole()->body)->toContain('Duke chased Stan');
});

it('only counts the work of the player, not of colleagues in the same branch', function () {
    $colleague = User::findOrFail(employAtSeededPosition('flowright-plumbing', 'office-assistant-2')->user_id);
    workAs($colleague, 'POST', 'api.v1.shift.clock-in');
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');
    handleHollisCase($colleague, $this->hollis, 'rita-vance', '2026-09-22', '10:00');

    workAs($this->player, 'POST', 'api.v1.shift.clock-out');

    expect(WorkCase::query()->whereBelongsTo($this->employment)->sole()->status)->toBe(WorkCaseStatus::Open)
        ->and(Email::query()->whereBelongsTo($this->player)->where('subject', 'Mrs. Hollis?')->exists())->toBeTrue();
});

it('keeps the case open and sends a reminder when a step is missing', function () {
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');
    workAs($this->player, 'POST', 'api.v1.appointments.store', ['customer_id' => $this->hollis->id, 'technician_id' => technician('rita-vance')->id, 'date' => '2026-09-22', 'slot' => '10:00']);
    workAs($this->player, 'POST', 'api.v1.emails.store', ['customer_id' => $this->hollis->id, 'subject' => 'Appointment', 'body' => 'See you.', 'action' => 'confirm_appointment']);

    workAs($this->player, 'POST', 'api.v1.shift.clock-out');

    expect(WorkCase::sole()->status)->toBe(WorkCaseStatus::Open)
        ->and(Email::query()->where('subject', 'Mrs. Hollis?')->exists())->toBeTrue()
        ->and(Email::query()->where('subject', 'Re: Mrs. Hollis')->exists())->toBeFalse();
});

it('waits for the technician visit before giving feedback', function () {
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');
    handleHollisCase($this->player, $this->hollis, 'rita-vance', '2026-09-22', '10:00');
    workAs($this->player, 'POST', 'api.v1.shift.clock-out');

    carryOutAppointmentsAt('2026-09-22 11:59:00');

    expect(WorkCase::sole()->status)->toBe(WorkCaseStatus::Open)
        ->and(Email::query()->where('subject', 'Mrs. Hollis?')->exists())->toBeFalse()
        ->and(Email::query()->where('subject', 'Re: Mrs. Hollis')->exists())->toBeFalse();
});

it('does not open the same case again on the next shift', function () {
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');
    workAs($this->player, 'POST', 'api.v1.shift.clock-out');
    $this->travelTo('2026-09-22 09:00:00');

    workAs($this->player, 'POST', 'api.v1.shift.clock-in');

    expect(WorkCase::count())->toBe(1)
        ->and(Email::query()->where('subject', 'Water under my kitchen sink!!')->count())->toBe(1);
});
