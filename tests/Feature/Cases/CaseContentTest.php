<?php

use App\Cases\CaseCatalog;
use App\Models\Company;
use App\Workplace\CompanySoftwareCatalog;
use Database\Seeders\CompanySeeder;
use Database\Seeders\CompanySoftwareSeeder;
use Illuminate\Support\Facades\File;

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

it('only references known colleagues and technicians in case messages', function () {
    $this->seed([CompanySeeder::class, CompanySoftwareSeeder::class]);

    foreach (Company::all() as $company) {
        foreach (app(CaseCatalog::class)->forCompany($company) as $definition) {
            foreach ($definition->messages as $message) {
                expect(app(CompanySoftwareCatalog::class)->colleagueName($company, $message->sender))->not->toBeEmpty();
            }
        }

        $path = database_path("content/cases/{$company->slug}.json");
        $content = File::exists($path) ? File::get($path) : '';
        preg_match_all('/"technician": "([a-z-]+)"/', $content, $technicians);

        foreach ($technicians[1] as $slug) {
            expect($company->technicians()->where('slug', $slug)->exists())->toBeTrue();
        }
    }
});
