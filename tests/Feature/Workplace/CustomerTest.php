<?php

use App\Models\Company;
use App\Models\Customer;

it('lists the customers of the employer', function () {
    $company = Company::factory()->create();
    Customer::factory()->for($company)->create(['name' => 'Margaret Hollis', 'notes' => 'Has a loud dog.']);
    Customer::factory()->create(['name' => 'Someone Elsewhere']);

    $this->actingAs(playerOnDutyAt($company))
        ->getJson(route('api.v1.customers.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Margaret Hollis')
        ->assertJsonPath('data.0.notes', 'Has a loud dog.');
});
