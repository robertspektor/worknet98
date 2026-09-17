<?php

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Person;

it('lists the customers of the own branch', function () {
    $branch = Branch::factory()->create();
    Customer::factory()->for($branch)->for(Person::factory()->named('Margaret Hollis'))->create(['notes' => 'Has a loud dog.']);
    Customer::factory()->for(Branch::factory()->for($branch->company))->for(Person::factory()->named('Someone In Another Branch'))->create();

    $this->actingAs(playerOnDutyAt($branch))
        ->getJson(route('api.v1.customers.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Margaret Hollis')
        ->assertJsonPath('data.0.notes', 'Has a loud dog.');
});
