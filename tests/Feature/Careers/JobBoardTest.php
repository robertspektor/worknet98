<?php

use App\Models\Company;
use App\Models\JobOpening;
use App\Models\User;

it('lists the job openings of companies that speak the player language', function () {
    $player = User::factory()->locale('de')->create();
    $german = Company::factory()->locale('de')->create(['name' => 'Rohrfuchs Sanitär']);
    JobOpening::factory()->for($german)->create(['title' => 'Bürokraft', 'daily_salary' => 95]);
    JobOpening::factory()->for(Company::factory()->locale('en'))->create(['title' => 'Office Assistant']);

    $this->actingAs($player)
        ->getJson(route('api.v1.job-openings.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Bürokraft')
        ->assertJsonPath('data.0.daily_salary', 95)
        ->assertJsonPath('data.0.company.name', 'Rohrfuchs Sanitär');
});

it('hides closed job openings', function () {
    $player = User::factory()->create();
    JobOpening::factory()->create(['title' => 'Office Assistant']);
    JobOpening::factory()->closed()->create(['title' => 'Helpdesk Associate']);

    $titles = $this->actingAs($player)->getJson(route('api.v1.job-openings.index'))->json('data.*.title');

    expect($titles)->toBe(['Office Assistant']);
});

it('orders job openings by company and title', function () {
    $player = User::factory()->create();
    $zeta = Company::factory()->create(['name' => 'Zeta Corp']);
    $alpha = Company::factory()->create(['name' => 'Alpha Inc']);
    JobOpening::factory()->for($zeta)->create(['title' => 'Clerk']);
    JobOpening::factory()->for($alpha)->create(['title' => 'Typist']);
    JobOpening::factory()->for($alpha)->create(['title' => 'Archivist']);

    $titles = $this->actingAs($player)->getJson(route('api.v1.job-openings.index'))->json('data.*.title');

    expect($titles)->toBe(['Archivist', 'Typist', 'Clerk']);
});

it('requires a signed-in player to browse job openings', function () {
    $this->getJson(route('api.v1.job-openings.index'))->assertUnauthorized();
});
