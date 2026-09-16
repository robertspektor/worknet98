<?php

use App\Cases\CaseCatalog;
use App\Models\Company;
use Database\Seeders\CompanySeeder;
use Database\Seeders\CompanySoftwareSeeder;

it('seeds technicians and customers for every company with company software', function () {
    $this->seed([CompanySeeder::class, CompanySoftwareSeeder::class]);

    foreach (['flowright-plumbing', 'rohr-und-sohn'] as $slug) {
        $company = Company::query()->where('slug', $slug)->sole();

        expect($company->technicians()->count())->toBeGreaterThan(0)
            ->and($company->customers()->count())->toBeGreaterThan(0)
            ->and($company->manager_name)->not->toBeNull();
    }
});

it('only references known customers in case content', function () {
    $this->seed([CompanySeeder::class, CompanySoftwareSeeder::class]);

    foreach (Company::all() as $company) {
        foreach (app(CaseCatalog::class)->forCompany($company) as $definition) {
            expect($company->customers()->where('slug', $definition->requestMail['customer'])->exists())->toBeTrue();
        }
    }
});
