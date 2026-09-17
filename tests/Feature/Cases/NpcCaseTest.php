<?php

use App\Cases\WorkCaseStatus;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\WorkCase;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CompanySeeder;

beforeEach(function () {
    $this->travelTo('2026-09-21 09:00:00');
    $this->seed([CompanySeeder::class, BranchSeeder::class]);
    config(['game.npc_case_delay_seconds' => 120]);
});

function openNpcCase(string $template, string $customer): WorkCase
{
    test()->artisan('cases:open', ['company' => 'flowright-plumbing', 'branch' => 'maple-falls', 'template' => $template, '--customer' => $customer])
        ->assertSuccessful();

    return WorkCase::query()->latest('id')->firstOrFail();
}

it('does nothing before the NPC gets to the case', function () {
    $workCase = openNpcCase('leaking-pipe', 'priya-raman');
    $this->travelTo('2026-09-21 09:01:59');

    $this->artisan('cases:work-npc-cases')->assertSuccessful();

    expect($workCase->fresh()?->status)->toBe(WorkCaseStatus::Open)
        ->and(Appointment::count())->toBe(0);
});

it('books the earliest free slot with a skilled technician when the customer is home', function () {
    $workCase = openNpcCase('leaking-pipe', 'priya-raman');
    $this->travelTo('2026-09-21 09:02:00');

    $this->artisan('cases:work-npc-cases')->assertSuccessful();

    $appointment = Appointment::sole();
    expect($workCase->fresh()?->status)->toBe(WorkCaseStatus::Resolved)
        ->and($appointment->booked_by_employment_id)->toBeNull()
        ->and($appointment->customer->slug)->toBe('priya-raman')
        ->and($appointment->technician->slug)->toBe('stan-kowalski')
        ->and($appointment->date->toDateString())->toBe('2026-09-22')
        ->and($appointment->slot)->toBe('13:00');
});

it('skips slots that are already booked', function () {
    $branch = Branch::query()->where('slug', 'maple-falls')->sole();
    Appointment::factory()->create([
        'branch_id' => $branch->id,
        'customer_id' => $branch->customers()->where('slug', 'walter-beck')->sole()->id,
        'technician_id' => technician('stan-kowalski')->id,
        'date' => '2026-09-22',
        'slot' => '13:00',
    ]);
    openNpcCase('leaking-pipe', 'priya-raman');
    $this->travelTo('2026-09-21 09:02:00');

    $this->artisan('cases:work-npc-cases')->assertSuccessful();

    $appointment = Appointment::query()->where('customer_id', $branch->customers()->where('slug', 'priya-raman')->sole()->id)->sole();
    expect($appointment->technician->slug)->toBe('stan-kowalski')
        ->and($appointment->slot)->toBe('15:00');
});

it('never works on cases of positions held by players', function () {
    employAtSeededPosition('flowright-plumbing');
    openNpcCase('leaking-pipe', 'priya-raman');
    $this->travelTo('2026-09-21 10:00:00');

    $this->artisan('cases:work-npc-cases')->assertSuccessful();

    expect(Appointment::count())->toBe(0);
});
