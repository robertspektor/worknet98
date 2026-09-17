<?php

use App\Models\Branch;
use App\Models\City;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Household;
use App\Models\Person;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CompanySeeder;

beforeEach(fn () => $this->seed([CompanySeeder::class, CitySeeder::class, BranchSeeder::class]));

it('seeds one city for every company language', function () {
    $locales = Company::query()->distinct()->orderBy('locale')->pluck('locale')->all();

    expect(City::query()->orderBy('locale')->pluck('locale')->all())->toBe($locales);
});

it('places every branch in the city that speaks the language of its company', function () {
    foreach (Branch::with(['company', 'city'])->get() as $branch) {
        expect($branch->city->locale)->toBe($branch->company->locale);
    }
});

it('lets one person be a customer at one company and work for another', function () {
    $martin = Person::query()->where('slug', 'martin-hollmeier')->sole();

    expect($martin->customerships()->sole()->branch->company->slug)->toBe('rohr-und-sohn')
        ->and($martin->positions()->sole()->branch->company->slug)->toBe('nordwerk-logistik');
});

it('lets colleagues and customers share a household', function () {
    $household = Person::query()->where('slug', 'bettina-mertens')->sole()->household;

    expect($household->members()->orderBy('slug')->pluck('slug')->all())->toBe(['bettina-mertens', 'gisela-mertens'])
        ->and($household->street)->toBe('Weidenweg 7');
});

it('gives every customer an e-mail address and a phone for the company software', function () {
    foreach (Customer::with('person.household')->get() as $customer) {
        expect($customer->person->email_address)->not->toBeNull()
            ->and($customer->person->household->phone)->not->toBeNull();
    }
});

it('seeds the named people idempotently', function () {
    $counts = [City::count(), Household::count(), Person::count(), Customer::count()];

    $this->seed([CitySeeder::class, BranchSeeder::class]);

    expect([City::count(), Household::count(), Person::count(), Customer::count()])->toBe($counts);
});
