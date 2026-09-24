<?php

use App\Models\User;

it('serves the description of the game in the delivered html', function () {
    $response = $this->get(route('landing'))->assertOk();

    expect($response->content())
        ->toContain('One computer. One life.')
        ->toContain('WorkNet 98')
        ->toContain('RetroTron Modular')
        ->toContain('<meta name="description"');
});

it('links the imprint and the privacy policy on every public page', function () {
    foreach (['landing', 'imprint', 'privacy'] as $page) {
        $this->get(route($page))
            ->assertOk()
            ->assertSee(route('imprint'))
            ->assertSee(route('privacy'));
    }
});

it('serves the landing page in the language the player picked', function () {
    $this->withSession(['locale' => 'de'])
        ->get(route('landing'))
        ->assertOk()
        ->assertSee('Ein Computer. Ein Leben.', false);
});

it('sends a guest who asks for the desk to the catalogue', function () {
    $this->get(route('home'))->assertRedirect(route('landing'));
    $this->get(route('office'))->assertRedirect(route('landing'));
});

it('sends a signed in player straight to their desk', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('landing'))
        ->assertRedirect(route('home'));
});

it('registers a player at the terminal and leaves the public page behind', function () {
    $this->post(route('sign-in.store'), [
        'email' => 'new@desklife98.test',
        'age_confirmed' => '1',
        'locale' => 'en',
    ])->assertRedirect(route('terminal'));

    expect(User::query()->where('email', 'new@desklife98.test')->exists())->toBeTrue();
});

it('leaves the public page with a hard visit so the game is not framed by it', function () {
    $this->withHeaders(['X-Inertia' => 'true', 'X-Inertia-Version' => ''])
        ->post(route('sign-in.store'), [
            'email' => 'hardvisit@desklife98.test',
            'age_confirmed' => '1',
            'locale' => 'en',
        ])
        ->assertStatus(409)
        ->assertHeader('X-Inertia-Location', route('terminal'));
});

it('shows the order form again with an error when the age is not confirmed', function () {
    $this->from(route('landing'))
        ->post(route('sign-in.store'), ['email' => 'nobody@desklife98.test', 'locale' => 'en'])
        ->assertRedirect(route('landing'))
        ->assertSessionHasErrors('age_confirmed');

    expect(User::query()->count())->toBe(0);
});
