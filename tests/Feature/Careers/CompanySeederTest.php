<?php

use App\Localization\SupportedLocales;
use App\Models\Company;
use App\Models\JobOpening;
use Database\Seeders\CompanySeeder;

it('seeds companies with job openings for every supported language', function () {
    $this->seed(CompanySeeder::class);

    foreach (app(SupportedLocales::class)->codes() as $locale) {
        expect(Company::query()->where('locale', $locale)->has('jobOpenings')->exists())->toBeTrue();
    }
});

it('opens positions only at playable companies', function () {
    $this->seed(CompanySeeder::class);

    $openCompanies = Company::query()->whereHas('jobOpenings', fn ($query) => $query->where('is_open', true))->pluck('slug')->sort()->values()->all();

    expect($openCompanies)->toBe(['flowright-plumbing', 'millbrook-city-hall', 'nordwerk-logistik', 'rohr-und-sohn', 'stadtverwaltung-lindenstadt', 'transglobal-logistics']);
});

it('can seed the companies repeatedly without duplicates', function () {
    $this->seed(CompanySeeder::class);
    $companies = Company::count();
    $openings = JobOpening::count();

    $this->seed(CompanySeeder::class);

    expect(Company::count())->toBe($companies)
        ->and(JobOpening::count())->toBe($openings);
});
