<?php

use App\Cases\Metric;
use App\Cases\MetricBook;
use App\Cases\WorkCaseStatus;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Email;
use App\Models\Employment;
use App\Models\JobOpening;
use App\Models\Technician;
use App\Models\User;
use App\Models\WorkCase;
use Database\Seeders\CompanySeeder;
use Database\Seeders\CompanySoftwareSeeder;

beforeEach(function () {
    $this->travelTo('2026-09-21 09:00:00');
    $this->seed([CompanySeeder::class, CompanySoftwareSeeder::class]);
    $company = Company::query()->where('slug', 'flowright-plumbing')->sole();
    $employment = Employment::factory()->create([
        'company_id' => $company->id,
        'job_opening_id' => JobOpening::query()->where('company_id', $company->id)->sole()->id,
    ]);
    $this->employment = $employment;
    $this->player = User::findOrFail($employment->user_id);
    $this->hollis = Customer::query()->where('slug', 'margaret-hollis')->sole();
});

function workAs(User $player, string $method, string $route, array $data = []): void
{
    test()->actingAs($player)->json($method, route($route), $data)->assertSuccessful();
}

function technician(string $slug): Technician
{
    return Technician::query()->where('slug', $slug)->sole();
}

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

    expect(app(MetricBook::class)->valueOf($this->employment, Metric::Cost))->toBe(-2)
        ->and(Email::query()->where('subject', 'Re: Mrs. Hollis')->sole()->body)->toContain('You sent a heating guy');
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

it('does not open the same case again on the next shift', function () {
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');
    workAs($this->player, 'POST', 'api.v1.shift.clock-out');
    $this->travelTo('2026-09-22 09:00:00');

    workAs($this->player, 'POST', 'api.v1.shift.clock-in');

    expect(WorkCase::count())->toBe(1)
        ->and(Email::query()->where('subject', 'Water under my kitchen sink!!')->count())->toBe(1);
});
