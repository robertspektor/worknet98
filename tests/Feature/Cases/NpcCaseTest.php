<?php

use App\Cases\Npc\NpcCaseWorker;
use App\Cases\WorkCaseStatus;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Email;
use App\Models\WorkCase;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CompanySeeder;

beforeEach(function () {
    $this->travelTo('2026-09-21 09:00:00');
    $this->seed([CompanySeeder::class, BranchSeeder::class]);
    config(['game.npc_case_delay_seconds' => 120]);
    $this->branch = Branch::query()->where('slug', 'maple-falls')->sole();
});

function openNpcCase(string $template, string $customer): WorkCase
{
    test()->artisan('cases:open', ['company' => 'flowright-plumbing', 'branch' => 'maple-falls', 'template' => $template, '--customer' => $customer])
        ->assertSuccessful();

    return WorkCase::query()->latest('id')->firstOrFail();
}

it('does nothing before the NPC gets to the case', function () {
    openNpcCase('leaking-pipe', 'priya-raman');
    $this->travelTo('2026-09-21 09:01:59');

    app(NpcCaseWorker::class)->workDue();

    expect(Appointment::count())->toBe(0);
});

it('books the earliest free slot with a skilled technician when the customer is home', function () {
    $workCase = openNpcCase('leaking-pipe', 'priya-raman');
    $this->travelTo('2026-09-21 09:02:00');

    app(NpcCaseWorker::class)->workDue();
    app(NpcCaseWorker::class)->workDue();

    $appointment = Appointment::sole();
    expect($workCase->fresh()?->status)->toBe(WorkCaseStatus::Open)
        ->and($appointment->booked_by_employment_id)->toBeNull()
        ->and($appointment->booked_by_position_id)->toBe($workCase->position_id)
        ->and($appointment->customer->slug)->toBe('priya-raman')
        ->and($appointment->technician->slug)->toBe('stan-kowalski')
        ->and($appointment->date->toDateString())->toBe('2026-09-22')
        ->and($appointment->slot)->toBe('13:00');
});

it('skips slots that are already booked', function () {
    Appointment::factory()->create([
        'branch_id' => $this->branch->id,
        'customer_id' => $this->branch->customers()->where('slug', 'walter-beck')->sole()->id,
        'technician_id' => technician('stan-kowalski')->id,
        'date' => '2026-09-22',
        'slot' => '13:00',
    ]);
    openNpcCase('leaking-pipe', 'priya-raman');
    $this->travelTo('2026-09-21 09:02:00');

    app(NpcCaseWorker::class)->workDue();

    $appointment = Appointment::query()->where('customer_id', $this->branch->customers()->where('slug', 'priya-raman')->sole()->id)->sole();
    expect($appointment->technician->slug)->toBe('stan-kowalski')
        ->and($appointment->slot)->toBe('15:00');
});

it('never works on cases of positions held by players', function () {
    employAtSeededPosition('flowright-plumbing');
    openNpcCase('leaking-pipe', 'priya-raman');
    $this->travelTo('2026-09-21 10:00:00');

    app(NpcCaseWorker::class)->workDue();

    expect(Appointment::count())->toBe(0);
});

it('resolves the NPC case quietly once the technician has been there', function () {
    $workCase = openNpcCase('leaking-pipe', 'priya-raman');
    $this->travelTo('2026-09-21 09:02:00');
    app(NpcCaseWorker::class)->workDue();

    carryOutAppointmentsAt('2026-09-22 15:00:00');

    expect($workCase->fresh()?->status)->toBe(WorkCaseStatus::Resolved)
        ->and(Appointment::sole()->executed_at)->not->toBeNull()
        ->and(Email::count())->toBe(0);
});
