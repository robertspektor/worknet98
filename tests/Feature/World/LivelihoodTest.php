<?php

use App\Models\City;
use App\Models\Person;
use App\Models\Position;
use App\World\Population\Occupation;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\PopulationSeeder;

beforeEach(fn () => $this->seed([CompanySeeder::class, CitySeeder::class, BranchSeeder::class]));

it('gives every named resident an occupation and an income', function () {
    expect(Person::query()->whereNull('occupation')->count())->toBe(0)
        ->and(Person::query()->whereNull('monthly_income')->count())->toBe(0);
});

it('employs the people who hold a position in their company', function () {
    $clerk = Position::query()->whereRelation('branch.company', 'slug', 'rohr-und-sohn')->where('slug', 'office-assistant-1')->sole();

    expect($clerk->person->occupation)->toBe(Occupation::Employed)
        ->and($clerk->person->profession)->toBe('Bürokraft Terminplanung')
        ->and($clerk->person->monthly_income)->toBe($clerk->daily_salary * 21);
});

it('mixes employed, unemployed, retired and training people in the generated population', function () {
    $this->seed(PopulationSeeder::class);
    $city = City::query()->where('slug', 'lindenstadt')->sole();
    $residents = Person::query()->whereBelongsTo($city)->count();

    foreach (Occupation::cases() as $occupation) {
        $share = Person::query()->whereBelongsTo($city)->where('occupation', $occupation)->count() / $residents;

        expect($share)->toBeGreaterThan(0.02);
    }

    expect(Person::query()->whereBelongsTo($city)->where('occupation', Occupation::Employed)->count() / $residents)->toBeGreaterThan(0.4)
        ->and(Person::query()->whereBelongsTo($city)->where('occupation', Occupation::Employed)->avg('monthly_income'))->toBeGreaterThan(1700.0);
});

it('gives employed and training people a profession, the others none', function () {
    $this->seed(PopulationSeeder::class);

    expect(Person::query()->whereIn('occupation', [Occupation::Employed, Occupation::InTraining])->whereNull('profession')->count())->toBe(0)
        ->and(Person::query()->whereIn('occupation', [Occupation::Unemployed, Occupation::Retired])->whereNotNull('profession')->count())->toBe(0);
});
