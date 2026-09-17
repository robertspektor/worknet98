<?php

use App\Models\Branch;
use App\Models\Customer;

it('lists the customers of the own branch', function () {
    $branch = Branch::factory()->create();
    Customer::factory()->for($branch)->create(['name' => 'Margaret Hollis', 'notes' => 'Has a loud dog.']);
    Customer::factory()->for(Branch::factory()->for($branch->company))->create(['name' => 'Someone In Another Branch']);

    $this->actingAs(playerOnDutyAt($branch))
        ->getJson(route('api.v1.customers.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Margaret Hollis')
        ->assertJsonPath('data.0.notes', 'Has a loud dog.');
});
