<?php

use App\Cases\Metric;
use App\Cases\MetricBook;
use App\Models\Email;
use App\Models\Employment;
use App\Models\LedgerEntry;
use App\Models\LivingCostBill;
use App\Models\Shift;
use App\Models\User;
use App\Work\LedgerReason;
use App\Work\Wallet;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CompanySeeder;

beforeEach(function () {
    $this->travelTo('2026-09-21 09:00:00');
    $this->seed([CompanySeeder::class, CitySeeder::class, BranchSeeder::class]);
    $this->employment = employAtSeededPosition('rohr-und-sohn');
    $this->player = User::findOrFail($this->employment->user_id);
    $this->player->update(['locale' => 'de']);
    $this->monthlySalary = $this->employment->daily_salary * 21;
});

function worked(Employment $employment, int $minutes): void
{
    Shift::factory()->create([
        'user_id' => $employment->user_id,
        'employment_id' => $employment->id,
        'clocked_in_at' => now(),
        'clocked_out_at' => now()->addMinutes($minutes),
        'worked_seconds' => $minutes * 60,
    ]);
}

it('pays the full contract salary when the target time is met', function () {
    worked($this->employment, 120);
    app(MetricBook::class)->apply($this->employment, [Metric::CustomerSatisfaction->value => 1]);

    $this->travelTo('2026-10-01 00:05:00');
    $this->artisan('game:tick')->assertSuccessful();

    $salary = LedgerEntry::query()->where('reason', LedgerReason::Salary)->sole();
    expect($salary->amount)->toBe($this->employment->daily_salary * 21)
        ->and(Email::query()->where('user_id', $this->player->id)->where('subject', 'Gehaltsabrechnung September 2026')->sole()->body)
        ->toContain('Aktive Zeit: 120 von 120 Minuten')
        ->toContain("Ausgezahlt: {$this->monthlySalary} C");
});

it('cuts the salary down when the player worked less than the contract asks', function () {
    worked($this->employment, 30);

    $this->travelTo('2026-10-01 00:05:00');
    $this->artisan('game:tick')->assertSuccessful();

    expect(LedgerEntry::query()->where('reason', LedgerReason::Salary)->sole()->amount)->toBe((int) round($this->employment->daily_salary * 21 * 0.25))
        ->and(Email::query()->where('user_id', $this->player->id)->where('subject', 'Gehaltsabrechnung September 2026')->sole()->body)
        ->toContain('unter der vereinbarten Zeit');
});

it('charges rent, electricity and phone once a month and tells the player about it', function () {
    worked($this->employment, 120);

    $this->travelTo('2026-10-01 00:05:00');
    $this->artisan('game:tick')->assertSuccessful();
    $this->artisan('game:tick')->assertSuccessful();

    expect(LivingCostBill::query()->where('user_id', $this->player->id)->sole()->amount)->toBe(750)
        ->and(LedgerEntry::query()->where('reason', LedgerReason::LivingCosts)->sum('amount'))->toBe(-750)
        ->and(Email::query()->where('user_id', $this->player->id)->where('subject', 'Miete September 2026')->exists())->toBeTrue()
        ->and(Email::query()->where('user_id', $this->player->id)->where('subject', 'Stromrechnung September 2026')->exists())->toBeTrue()
        ->and(app(Wallet::class)->balanceOf($this->player))->toBe($this->monthlySalary - 750);
});

it('bills players without a job as well', function () {
    $unemployed = User::factory()->create(['locale' => 'de']);

    $this->travelTo('2026-10-01 00:05:00');
    $this->artisan('game:tick')->assertSuccessful();

    expect(app(Wallet::class)->balanceOf($unemployed))->toBe(-750);
});
