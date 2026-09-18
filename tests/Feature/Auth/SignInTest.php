<?php

use App\Mail\LoginLinkMail;
use App\Models\LoginLink;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(fn () => Mail::fake());

it('registers a new player and signs them in without a confirmation mail', function () {
    $this->post(route('sign-in.store'), [
        'email' => 'Gary@Example.com',
        'age_confirmed' => '1',
        'locale' => 'de',
    ])->assertRedirect(route('home'))->assertSessionHas('status', 'booted');

    $player = User::sole();
    expect($player->email)->toBe('gary@example.com')
        ->and($player->locale)->toBe('de')
        ->and($player->age_confirmed_at)->not->toBeNull()
        ->and($player->email_verified_at)->toBeNull();
    $this->assertAuthenticatedAs($player);
    Mail::assertNothingSent();
    expect(LoginLink::count())->toBe(0);
});

it('sends a login link instead of signing in when the address already has a player', function () {
    $player = User::factory()->locale('en')->create(['email' => 'gary@example.com']);

    $this->post(route('sign-in.store'), [
        'email' => 'GARY@example.com',
        'age_confirmed' => '1',
        'locale' => 'de',
    ])->assertRedirect()->assertSessionHas('status', 'login-link-sent');

    $this->assertGuest();
    expect(User::sole()->is($player))->toBeTrue();
    Mail::assertSent(LoginLinkMail::class, fn (LoginLinkMail $mail): bool => $mail->hasTo('GARY@example.com')
        && $mail->locale === 'de'
        && str_contains($mail->url, '/login/'));

    $link = LoginLink::sole();
    expect($link->email)->toBe('gary@example.com')
        ->and($link->token_hash)->toHaveLength(64)
        ->and($link->expires_at->isFuture())->toBeTrue();
});

it('requires the age confirmation', function () {
    $this->post(route('sign-in.store'), [
        'email' => 'kid@example.com',
        'locale' => 'en',
    ])->assertSessionHasErrors(['age_confirmed' => 'You must be at least 16 years old to play.']);

    $this->assertGuest();
    expect(User::count())->toBe(0);
});

it('rejects invalid e-mail addresses and unsupported languages', function () {
    $this->post(route('sign-in.store'), [
        'email' => 'not-an-email',
        'age_confirmed' => '1',
        'locale' => 'xx',
    ])->assertSessionHasErrors(['email', 'locale']);

    $this->assertGuest();
    expect(User::count())->toBe(0);
});

it('throttles repeated requests for the same address', function () {
    User::factory()->create(['email' => 'spam@example.com']);
    $payload = ['email' => 'spam@example.com', 'age_confirmed' => '1', 'locale' => 'en'];

    foreach (range(1, 3) as $attempt) {
        $this->post(route('sign-in.store'), $payload)->assertRedirect();
    }

    $this->post(route('sign-in.store'), $payload)->assertTooManyRequests();
});

it('shows validation messages in the requested language', function () {
    $this->withHeader('Accept-Language', 'de')
        ->post(route('sign-in.store'), ['email' => 'kid@example.com', 'locale' => 'de'])
        ->assertSessionHasErrors(['age_confirmed' => 'Du musst mindestens 16 Jahre alt sein, um zu spielen.']);
});
