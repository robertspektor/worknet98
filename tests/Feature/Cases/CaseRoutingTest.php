<?php

use App\Cases\WorkCaseStatus;
use App\Models\Email;
use App\Models\WorkCase;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CompanySeeder;

beforeEach(function () {
    $this->travelTo('2026-09-21 09:00:00');
    $this->seed([CompanySeeder::class, CitySeeder::class, BranchSeeder::class]);
});

function openCase(string $template, string $customer): WorkCase
{
    test()->artisan('cases:open', ['company' => 'flowright-plumbing', 'branch' => 'maple-falls', 'template' => $template, '--customer' => $customer])
        ->assertSuccessful();

    return WorkCase::query()->latest('id')->firstOrFail();
}

it('routes new cases to the responsible position with the fewest open cases', function () {
    $assignees = collect(['walter-beck', 'priya-raman', 'denise-okafor', 'martin-hollister'])
        ->map(fn (string $customer): string => openCase('leaking-pipe', $customer)->position->slug);

    expect($assignees->all())->toBe(['office-assistant-1', 'office-assistant-2', 'office-assistant-3', 'office-assistant-1']);
});

it('sends the customer request to the player holding the assigned position', function () {
    $employment = employAtSeededPosition('flowright-plumbing');

    $workCase = openCase('leaking-pipe', 'priya-raman');

    $request = Email::sole();
    expect($workCase->employment_id)->toBe($employment->id)
        ->and($workCase->npc_due_at)->toBeNull()
        ->and($request->employment_id)->toBe($employment->id)
        ->and($request->sender_name)->toBe('Priya Raman')
        ->and($request->subject)->toBe('Pipe is leaking')
        ->and($request->body)->toContain('I am only home in the afternoons')
        ->and($request->body)->toEndWith('Priya Raman');
});

it('leaves a case at an NPC position to the NPC after a delay', function () {
    config(['game.npc_case_delay_seconds' => 120]);

    $workCase = openCase('leaking-pipe', 'priya-raman');

    expect($workCase->employment_id)->toBeNull()
        ->and($workCase->status)->toBe(WorkCaseStatus::Open)
        ->and($workCase->npc_due_at?->toDateTimeString())->toBe('2026-09-21 09:02:00')
        ->and(Email::count())->toBe(0);
});

it('refuses a customer that already has an open case', function () {
    openCase('leaking-pipe', 'priya-raman');

    $this->artisan('cases:open', ['company' => 'flowright-plumbing', 'branch' => 'maple-falls', 'template' => 'cold-radiator', '--customer' => 'priya-raman'])
        ->assertFailed();

    expect(WorkCase::count())->toBe(1);
});

it('picks a customer without an open case when none is given', function () {
    $this->artisan('cases:open', ['company' => 'flowright-plumbing', 'branch' => 'maple-falls', 'template' => 'leaking-pipe'])->assertSuccessful();
    $this->artisan('cases:open', ['company' => 'flowright-plumbing', 'branch' => 'maple-falls', 'template' => 'leaking-pipe'])->assertSuccessful();

    expect(WorkCase::query()->pluck('customer_id')->unique())->toHaveCount(2);
});
