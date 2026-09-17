<?php

use App\Cases\Metric;
use App\Cases\MetricBook;
use App\Cases\WorkCaseKind;
use App\Cases\WorkCaseStatus;
use App\Models\Customer;
use App\Models\Email;
use App\Models\User;
use App\Models\WorkCase;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CompanySeeder;

beforeEach(function () {
    $this->travelTo('2026-09-21 09:00:00');
    $this->seed([CompanySeeder::class, CitySeeder::class, BranchSeeder::class]);
    $this->employment = employAtSeededPosition('flowright-plumbing');
    $this->player = User::findOrFail($this->employment->user_id);
    $this->artisan('cases:open', ['company' => 'flowright-plumbing', 'branch' => 'maple-falls', 'template' => 'leaking-pipe', '--customer' => 'priya-raman'])->assertSuccessful();
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');
    $this->priya = $this->employment->branch()->customers()->ofPerson('priya-raman')->sole();
});

function handleTemplateCase(User $player, Customer $customer, string $technician, string $date, string $slot): void
{
    workAs($player, 'POST', 'api.v1.appointments.store', ['customer_id' => $customer->id, 'technician_id' => technician($technician)->id, 'date' => $date, 'slot' => $slot]);
    workAs($player, 'POST', 'api.v1.emails.store', ['customer_id' => $customer->id, 'subject' => 'Appointment', 'body' => 'See you then.', 'action' => 'confirm_appointment']);
    workAs($player, 'POST', 'api.v1.calendar-entries.store', ['date' => $date, 'time' => $slot, 'title' => 'Leak']);
}

it('resolves a case built from a template with feedback derived from the customer', function () {
    handleTemplateCase($this->player, $this->priya, 'stan-kowalski', '2026-09-22', '13:00');
    workAs($this->player, 'POST', 'api.v1.shift.clock-out');

    carryOutAppointmentsAt('2026-09-22 15:00:00');

    $metrics = app(MetricBook::class);
    expect(WorkCase::query()->where('kind', WorkCaseKind::Template)->sole()->status)->toBe(WorkCaseStatus::Resolved)
        ->and($metrics->valueOf($this->employment, Metric::CustomerSatisfaction))->toBe(2)
        ->and($metrics->valueOf($this->employment, Metric::Cost))->toBe(0)
        ->and($metrics->valueOf($this->employment, Metric::Punctuality))->toBe(1);

    $feedback = Email::query()->where('subject', 'Re: Priya Raman')->sole();
    expect($feedback->sender_name)->toBe('Gary Flowright')
        ->and($feedback->body)->toContain("about the leak at Priya Raman's")
        ->and($feedback->body)->toContain('Priya Raman says the time was perfect')
        ->and($feedback->body)->toContain('Right technician for a pipe');
});

it('lowers the metrics when the customer is not home and the technician lacks the skill', function () {
    handleTemplateCase($this->player, $this->priya, 'doug-pruitt', '2026-09-24', '08:00');
    workAs($this->player, 'POST', 'api.v1.shift.clock-out');

    carryOutAppointmentsAt('2026-09-24 10:00:00');

    $metrics = app(MetricBook::class);
    expect($metrics->valueOf($this->employment, Metric::CustomerSatisfaction))->toBe(-2)
        ->and($metrics->valueOf($this->employment, Metric::Cost))->toBe(-2)
        ->and($metrics->valueOf($this->employment, Metric::Punctuality))->toBe(-1)
        ->and(Email::query()->where('subject', 'Re: Priya Raman')->sole()->body)->toContain('Nobody was home');
});

it('reminds the player when the case is not handled', function () {
    workAs($this->player, 'POST', 'api.v1.shift.clock-out');

    expect(WorkCase::query()->where('kind', WorkCaseKind::Template)->sole()->status)->toBe(WorkCaseStatus::Open)
        ->and(Email::query()->where('subject', 'Priya Raman?')->sole()->body)->toContain('Priya Raman is still waiting');
});
