<?php

use App\Models\City;
use App\Models\Household;
use App\Models\Person;
use App\World\CityCatalog;
use App\World\HouseholdRecord;
use App\World\Population\NamePool;
use App\World\Population\PopulationPlanner;
use Database\Seeders\CitySeeder;
use Database\Seeders\PopulationSeeder;

function planLindenstadt(int $residents): array
{
    $catalog = app(CityCatalog::class);

    return app(PopulationPlanner::class)->plan(
        citySlug: 'lindenstadt',
        districts: ['Ahornfelde', 'Grünau'],
        residents: $residents,
        pool: NamePool::forLocale('de'),
        namedHouseholds: $catalog->namedHouseholdsOf('lindenstadt'),
    );
}

it('plans exactly the requested number of residents in households of one to five', function () {
    $households = planLindenstadt(500);

    expect(array_sum(array_map(fn (HouseholdRecord $household): int => count($household->members), $households)))->toBe(500);

    foreach ($households as $household) {
        expect(count($household->members))->toBeBetween(1, 5);
    }
});

it('plans the same population for the same city every time', function () {
    expect(planLindenstadt(200))->toEqual(planLindenstadt(200));
});

it('never reuses an address or a person of the named households', function () {
    $named = app(CityCatalog::class)->namedHouseholdsOf('lindenstadt');
    $households = [...$named, ...planLindenstadt(1500)];
    $slugs = array_merge(...array_map(fn (HouseholdRecord $household): array => array_column($household->members, 'slug'), $households));

    expect(array_unique(array_map(fn (HouseholdRecord $household): string => $household->addressKey(), $households)))->toHaveCount(count($households))
        ->and(array_unique($slugs))->toHaveCount(count($slugs));
});

it('leaves most residents without e-mail and some households without a phone', function () {
    $households = planLindenstadt(1000);
    $members = array_merge(...array_map(fn (HouseholdRecord $household): array => $household->members, $households));
    $withEmail = count(array_filter($members, fn ($member): bool => $member->emailAddress !== null));
    $withoutPhone = count(array_filter($households, fn (HouseholdRecord $household): bool => $household->phone === null));

    expect($withEmail)->toBeGreaterThan(0)->toBeLessThan(500)
        ->and($withoutPhone)->toBeGreaterThan(0);
});

it('seeds the configured population of every city next to its named people idempotently', function () {
    $this->seed([CitySeeder::class, PopulationSeeder::class]);
    $counts = [Household::count(), Person::count()];

    foreach (app(CityCatalog::class)->cities() as $content) {
        $city = City::query()->where('slug', $content['slug'])->sole();
        $named = array_sum(array_map(fn (HouseholdRecord $household): int => count($household->members), app(CityCatalog::class)->namedHouseholdsOf($city->slug)));

        expect($city->people()->count())->toBe($named + $content['population']);
    }

    $this->seed(PopulationSeeder::class);

    expect([Household::count(), Person::count()])->toBe($counts);
});
