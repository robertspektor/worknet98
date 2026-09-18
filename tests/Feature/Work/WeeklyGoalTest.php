<?php

use App\Models\Email;
use App\Models\LedgerEntry;
use App\Models\User;
use App\Models\WeeklyGoal;
use App\Work\LedgerReason;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CompanySeeder;

beforeEach(function () {
    $this->travelTo('2026-09-23 09:00:00');
    $this->seed([CompanySeeder::class, CitySeeder::class, BranchSeeder::class]);
    config(['game.work.weekly_cases' => 3]);
    $this->employment = employAtSeededPosition('flowright-plumbing');
    $this->player = User::findOrFail($this->employment->user_id);
});

it('counts the cases the player closed this week towards the weekly goal', function () {
    closeCases($this->employment, 2);

    $this->actingAs($this->player)->getJson(route('api.v1.shift.show'))
        ->assertOk()
        ->assertJsonPath('data.weekly_goal.target', 3)
        ->assertJsonPath('data.weekly_goal.resolved_cases', 2)
        ->assertJsonPath('data.weekly_goal.bonus', $this->employment->daily_salary)
        ->assertJsonPath('data.weekly_goal.achieved', false);
});

it('pays the weekly bonus once when the player reaches the goal', function () {
    closeCases($this->employment, 3);

    $this->artisan('game:tick')->assertSuccessful();
    $this->artisan('game:tick')->assertSuccessful();

    $mail = Email::query()->whereBelongsTo($this->player)->where('subject', 'Week done')->sole();
    expect(LedgerEntry::query()->where('reason', LedgerReason::Bonus)->sole()->amount)->toBe($this->employment->daily_salary)
        ->and(WeeklyGoal::query()->where('employment_id', $this->employment->id)->sole()->week)->toBe('2026-W39')
        ->and($mail->sender_name)->toBe('Gary Flowright')
        ->and($mail->body)->toContain('3 cases closed this week')
        ->and($mail->body)->toContain("bonus of {$this->employment->daily_salary} C");

    $this->actingAs($this->player)->getJson(route('api.v1.shift.show'))
        ->assertOk()
        ->assertJsonPath('data.weekly_goal.achieved', true);
});

it('pays nothing while the player stays below the goal', function () {
    closeCases($this->employment, 2);

    $this->artisan('game:tick')->assertSuccessful();

    expect(LedgerEntry::query()->where('reason', LedgerReason::Bonus)->exists())->toBeFalse()
        ->and(WeeklyGoal::query()->exists())->toBeFalse();
});

it('starts a fresh goal in the next game week', function () {
    closeCases($this->employment, 3);
    $this->artisan('game:tick')->assertSuccessful();

    $this->travelTo('2026-09-28 09:00:00');

    $this->actingAs($this->player)->getJson(route('api.v1.shift.show'))
        ->assertOk()
        ->assertJsonPath('data.weekly_goal.resolved_cases', 0)
        ->assertJsonPath('data.weekly_goal.achieved', false)
        ->assertJsonPath('data.weekly_goal.target', 3);
});
