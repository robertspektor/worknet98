<?php

use App\Milestones\MilestoneCatalog;
use App\Models\Email;
use App\Models\LedgerEntry;
use App\Models\PlayerMilestone;
use App\Models\Shift;
use App\Models\User;
use App\Work\LedgerReason;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CompanySeeder;

beforeEach(function () {
    $this->travelTo('2026-09-23 09:00:00');
    $this->seed([CompanySeeder::class, CitySeeder::class, BranchSeeder::class]);
    $this->employment = employAtSeededPosition('flowright-plumbing');
    $this->player = User::findOrFail($this->employment->user_id);
});

function achievedKeys(User $player): array
{
    return PlayerMilestone::query()->whereBelongsTo($player)->pluck('milestone_key')->all();
}

it('awards the first job the moment a player is hired', function () {
    $this->artisan('game:tick')->assertSuccessful();

    expect(achievedKeys($this->player))->toContain('first_job');
});

it('sends a letter only for milestones that have a sender', function () {
    Shift::factory()->create(['employment_id' => $this->employment->id]);

    $this->artisan('game:tick')->assertSuccessful();

    $mail = Email::query()->whereBelongsTo($this->player)->where('subject', 'Welcome to working life')->sole();

    expect(achievedKeys($this->player))->toContain('first_shift')
        ->and($mail->sender_name)->toBe('WorkNet 98')
        ->and($mail->body)->toContain('Connecting people to opportunity since 1987')
        ->and($mail->employment_id)->toBeNull()
        ->and(Email::query()->whereBelongsTo($this->player)->where('subject', 'like', '%shift%')->exists())->toBeFalse();
});

it('awards a balance milestone once the wallet passes the threshold', function () {
    LedgerEntry::create(['user_id' => $this->player->id, 'amount' => 499, 'reason' => LedgerReason::Salary]);
    $this->artisan('game:tick')->assertSuccessful();

    expect(achievedKeys($this->player))->not->toContain('savings_500');

    LedgerEntry::create(['user_id' => $this->player->id, 'amount' => 1, 'reason' => LedgerReason::Salary]);
    $this->artisan('game:tick')->assertSuccessful();

    $mail = Email::query()->whereBelongsTo($this->player)->where('subject', 'Balance of 500 C')->sole();

    expect(achievedKeys($this->player))->toContain('savings_500')
        ->and($mail->sender_name)->toBe('Millbrook Savings Bank');
});

it('never awards the same milestone twice', function () {
    $this->artisan('game:tick')->assertSuccessful();
    $this->artisan('game:tick')->assertSuccessful();

    expect(PlayerMilestone::query()->whereBelongsTo($this->player)->where('milestone_key', 'first_job')->count())->toBe(1)
        ->and(Email::query()->whereBelongsTo($this->player)->where('subject', 'Welcome to working life')->count())->toBe(1);
});

it('lists every milestone with the reached ones dated', function () {
    $this->artisan('game:tick')->assertSuccessful();

    $response = $this->actingAs($this->player)->getJson(route('api.v1.milestones.index'))->assertOk();
    $milestones = collect($response->json('data'));

    expect($milestones)->toHaveCount(15)
        ->and($milestones->firstWhere('key', 'first_job'))
        ->toMatchArray(['key' => 'first_job', 'achieved' => true])
        ->and($milestones->firstWhere('key', 'first_job')['achieved_at'])->not->toBeNull()
        ->and($milestones->firstWhere('key', 'savings_50000'))
        ->toMatchArray(['key' => 'savings_50000', 'achieved' => false, 'achieved_at' => null]);
});

it('has a translated title and hint for every milestone in both languages', function () {
    $keys = collect(app(MilestoneCatalog::class)->all())->pluck('key');

    foreach (['en', 'de'] as $locale) {
        foreach ($keys as $key) {
            expect(__("milestone.{$key}.title", [], $locale))->not->toBe("milestone.{$key}.title")
                ->and(__("milestone.{$key}.hint", [], $locale))->not->toBe("milestone.{$key}.hint");
        }
    }
});

it('awards the case milestones from the cases the player closed', function () {
    closeCases($this->employment, 10);

    $this->artisan('game:tick')->assertSuccessful();

    expect(achievedKeys($this->player))->toContain('first_case')
        ->and(achievedKeys($this->player))->toContain('ten_cases')
        ->and(achievedKeys($this->player))->not->toContain('fifty_cases');
});
