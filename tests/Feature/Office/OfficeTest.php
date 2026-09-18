<?php

use App\Models\Branch;
use App\Models\Company;
use App\Models\Employment;
use App\Models\Position;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('shows the office with the workplace of an employed player', function () {
    $company = Company::factory()->create(['name' => 'TransGlobal Logistics']);
    $position = Position::factory()->for(Branch::factory()->for($company))->create(['title' => 'Junior Dispatcher']);
    $player = User::factory()->create();
    Employment::factory()->at($position)->create(['user_id' => $player->id]);

    $this->actingAs($player)
        ->get(route('office'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('office')
            ->where('workplace', ['company' => 'TransGlobal Logistics', 'jobTitle' => 'Junior Dispatcher', 'award' => null]));
});

it('sends unemployed players back home', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('office'))
        ->assertRedirect(route('home'));
});

it('sends guests to the catalogue', function () {
    $this->get(route('office'))->assertRedirect(route('landing'));
});
