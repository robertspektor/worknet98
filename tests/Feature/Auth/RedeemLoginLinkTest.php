<?php

use App\Models\LoginLink;
use App\Models\User;

it('shows a confirmation page instead of consuming the link on GET', function () {
    LoginLink::factory()->forToken('scanner-safe-token')->create();

    $this->get(route('login.show', 'scanner-safe-token'))
        ->assertOk()
        ->assertSee(route('login.store', 'scanner-safe-token'));

    expect(LoginLink::sole()->consumed_at)->toBeNull();
});

it('creates a verified player and signs them in', function () {
    LoginLink::factory()->forToken('first-day')->create(['email' => 'new@example.com', 'locale' => 'de']);

    $this->post(route('login.store', 'first-day'))
        ->assertRedirect(route('home'))
        ->assertSessionHas('status', 'signed-in');

    $player = User::sole();
    expect($player->email)->toBe('new@example.com')
        ->and($player->locale)->toBe('de')
        ->and($player->email_verified_at)->not->toBeNull()
        ->and($player->age_confirmed_at)->not->toBeNull()
        ->and(LoginLink::sole()->consumed_at)->not->toBeNull();
    $this->assertAuthenticatedAs($player);
});

it('signs in an existing player without changing their language', function () {
    $player = User::factory()->locale('en')->create(['email' => 'back@example.com']);
    LoginLink::factory()->forToken('welcome-back')->create(['email' => 'back@example.com', 'locale' => 'de']);

    $this->post(route('login.store', 'welcome-back'))->assertRedirect(route('home'));

    expect($player->fresh()?->locale)->toBe('en');
    $this->assertAuthenticatedAs($player);
});

it('refuses links that are expired, used or unknown', function (string $token) {
    LoginLink::factory()->forToken('expired')->expired()->create();
    LoginLink::factory()->forToken('used')->consumed()->create();

    $this->post(route('login.store', $token))
        ->assertRedirect(route('landing'))
        ->assertSessionHas('status', 'login-link-invalid');

    $this->assertGuest();
})->with(['expired', 'used', 'unknown']);

it('works only once', function () {
    LoginLink::factory()->forToken('once')->create();

    $this->post(route('login.store', 'once'));
    $this->post(route('logout'));
    $this->post(route('login.store', 'once'))->assertSessionHas('status', 'login-link-invalid');

    $this->assertGuest();
});

it('keeps the player language after logging out', function () {
    $this->actingAs(User::factory()->locale('en')->create())
        ->withHeader('Accept-Language', 'de')
        ->post(route('logout'));

    $this->withHeader('Accept-Language', 'de')
        ->get(route('landing'))
        ->assertOk()
        ->assertSee('One computer. One job. One life.', false);
});

it('logs a player out', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('logout'))
        ->assertRedirect(route('landing'));

    $this->assertGuest();
});
