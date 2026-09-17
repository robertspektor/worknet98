<?php

use App\Models\Branch;
use App\Models\Company;
use App\Models\JobOpening;
use App\Models\Position;
use App\Workplace\CompanySoftwareCatalog;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CompanySeeder;
use Illuminate\Support\Collection;

beforeEach(fn () => $this->seed([CompanySeeder::class, CitySeeder::class, BranchSeeder::class]));

it('seeds a branch with an org chart for every company', function () {
    foreach (Company::all() as $company) {
        $branches = $company->branches()->get();

        expect($branches)->not->toBeEmpty();

        foreach ($branches as $branch) {
            expect($branch->positions()->whereNull('reports_to_position_id')->count())->toBe(1);
        }
    }
});

it('seeds technicians and customers for every branch whose company runs a scheduler', function () {
    foreach (branchesRunning('scheduler') as $branch) {
        expect($branch->technicians()->count())->toBeGreaterThan(0)
            ->and($branch->customers()->count())->toBeGreaterThan(0);
    }
});

it('seeds drivers for every branch whose company runs a route planner', function () {
    foreach (branchesRunning('tours') as $branch) {
        expect($branch->drivers()->count())->toBeGreaterThan(0);
    }
});

/**
 * @return Collection<int, Branch>
 */
function branchesRunning(string $software): Collection
{
    $branches = Branch::with('company')->get()
        ->filter(fn (Branch $branch): bool => array_key_exists($software, app(CompanySoftwareCatalog::class)->appNamesFor($branch->company)));

    expect($branches)->not->toBeEmpty();

    return $branches;
}

it('offers vacant positions with a superior for every job opening', function () {
    foreach (JobOpening::all() as $opening) {
        $positions = Position::query()->whereBelongsTo($opening)->vacant()->get();

        expect($positions)->not->toBeEmpty()
            ->and($positions->every(fn (Position $position): bool => $position->reports_to_position_id !== null))->toBeTrue();
    }
});

it('seeds the branches idempotently', function () {
    $counts = [Branch::count(), Position::count()];

    $this->seed(BranchSeeder::class);

    expect([Branch::count(), Position::count()])->toBe($counts);
});
