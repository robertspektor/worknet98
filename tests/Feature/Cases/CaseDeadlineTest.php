<?php

use App\Cases\Metric;
use App\Cases\MetricBook;
use App\Cases\WorkCaseKind;
use App\Cases\WorkCaseStatus;
use App\Models\Email;
use App\Models\User;
use App\Models\WorkCase;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CompanySeeder;

beforeEach(function () {
    $this->travelTo('2026-09-21 09:00:00');
    $this->seed([CompanySeeder::class, CitySeeder::class, BranchSeeder::class]);
    config(['game.npc_case_delay_seconds' => 120]);
    $this->employment = employAtSeededPosition('flowright-plumbing');
    $this->player = User::findOrFail($this->employment->user_id);
    $this->artisan('cases:open', ['company' => 'flowright-plumbing', 'branch' => 'maple-falls', 'template' => 'leaking-pipe', '--customer' => 'priya-raman'])->assertSuccessful();
    $this->workCase = WorkCase::query()->where('kind', WorkCaseKind::Template)->sole();
    $this->priya = $this->workCase->customer;
});

function workShift(User $player, string $day): void
{
    test()->travelTo("{$day} 09:00:00");
    workAs($player, 'POST', 'api.v1.shift.clock-in');
    test()->travelTo("{$day} 17:00:00");
    workAs($player, 'POST', 'api.v1.shift.clock-out');
}

it('only reminds the player after the first shift that ends without a booking', function () {
    workShift($this->player, '2026-09-21');

    expect($this->workCase->fresh()?->employment_id)->toBe($this->employment->id)
        ->and(Email::query()->where('subject', 'Priya Raman?')->exists())->toBeTrue();
});

it('hands the case to an NPC colleague when the next shift also ends without a booking', function () {
    workShift($this->player, '2026-09-21');
    workShift($this->player, '2026-09-22');

    $workCase = $this->workCase->fresh();
    expect($workCase?->status)->toBe(WorkCaseStatus::Open)
        ->and($workCase?->employment_id)->toBeNull()
        ->and($workCase?->position->slug)->toBe('office-assistant-2')
        ->and($workCase?->npc_due_at)->not->toBeNull()
        ->and(WorkCase::query()->where('kind', WorkCaseKind::Scripted)->sole()->status)->toBe(WorkCaseStatus::Lost)
        ->and(app(MetricBook::class)->valueOf($this->employment, Metric::Reliability))->toBe(-2);

    $mail = Email::query()->whereBelongsTo($this->player)->where('subject', 'Priya Raman: handed over')->sole();
    expect($mail->sender_name)->toBe('Gary Flowright')
        ->and($mail->body)->toContain('Judy Pham');
});

it('never takes away a case the player has booked', function () {
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');
    workAs($this->player, 'POST', 'api.v1.appointments.store', ['customer_id' => $this->priya->id, 'technician_id' => technician('stan-kowalski')->id, 'date' => '2026-09-22', 'slot' => '13:00']);
    $this->travelTo('2026-09-21 17:00:00');
    workAs($this->player, 'POST', 'api.v1.shift.clock-out');
    workShift($this->player, '2026-09-22');

    expect($this->workCase->fresh()?->employment_id)->toBe($this->employment->id);
});

it('hands over cases of players who stop showing up after 48 hours', function () {
    $this->travelTo('2026-09-23 08:59:59');
    $this->artisan('game:tick')->assertSuccessful();
    expect($this->workCase->fresh()?->employment_id)->toBe($this->employment->id);

    $this->travelTo('2026-09-23 09:00:00');
    $this->artisan('game:tick')->assertSuccessful();
    expect($this->workCase->fresh()?->employment_id)->toBeNull();
});

it('loses the customer when no NPC colleague can take over', function () {
    employAtSeededPosition('flowright-plumbing', 'office-assistant-2');
    employAtSeededPosition('flowright-plumbing', 'office-assistant-3');

    workShift($this->player, '2026-09-21');
    workShift($this->player, '2026-09-22');

    expect($this->workCase->fresh()?->status)->toBe(WorkCaseStatus::Lost)
        ->and(Email::query()->whereBelongsTo($this->player)->where('subject', 'Priya Raman: lost')->sole()->body)->toContain('called another company');
});

it('counts urgency from the shift in which the player first sees the case', function () {
    $this->travelTo('2026-09-23 09:00:00');
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');
    workAs($this->player, 'POST', 'api.v1.appointments.store', ['customer_id' => $this->priya->id, 'technician_id' => technician('stan-kowalski')->id, 'date' => '2026-09-25', 'slot' => '13:00']);
    workAs($this->player, 'POST', 'api.v1.emails.store', ['customer_id' => $this->priya->id, 'subject' => 'Appointment', 'body' => 'See you.', 'action' => 'confirm_appointment']);
    workAs($this->player, 'POST', 'api.v1.calendar-entries.store', ['date' => '2026-09-25', 'time' => '13:00', 'title' => 'Leak']);
    workAs($this->player, 'POST', 'api.v1.shift.clock-out');

    carryOutAppointmentsAt('2026-09-25 15:00:00');

    expect(app(MetricBook::class)->valueOf($this->employment, Metric::Punctuality))->toBe(1);
});
