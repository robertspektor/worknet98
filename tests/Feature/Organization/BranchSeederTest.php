<?php

use App\Models\Branch;
use App\Models\Company;
use App\Models\JobOpening;
use App\Models\Position;
use App\Workplace\CompanySoftwareCatalog;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CompanySeeder;

beforeEach(fn () => $this->seed([CompanySeeder::class, BranchSeeder::class]));

it('seeds a branch with an org chart for every company', function () {
    foreach (Company::all() as $company) {
        $branches = $company->branches()->get();

        expect($branches)->not->toBeEmpty();

        foreach ($branches as $branch) {
            expect($branch->positions()->whereNull('reports_to_position_id')->count())->toBe(1);
        }
    }
});

it('seeds technicians and customers for every branch of a company with company software', function () {
    $branches = Branch::with('company')->get()
        ->filter(fn (Branch $branch): bool => app(CompanySoftwareCatalog::class)->appNamesFor($branch->company) !== []);

    expect($branches)->not->toBeEmpty();

    foreach ($branches as $branch) {
        expect($branch->technicians()->count())->toBeGreaterThan(0)
            ->and($branch->customers()->count())->toBeGreaterThan(0);
    }
});

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
