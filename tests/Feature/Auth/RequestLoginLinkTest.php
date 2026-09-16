<?php

use App\Mail\LoginLinkMail;
use App\Models\LoginLink;
use Illuminate\Support\Facades\Mail;

beforeEach(fn () => Mail::fake());

it('sends a single-use login link in the chosen language', function () {
    $this->post(route('login-link.store'), [
        'email' => 'Gary@Example.com',
        'age_confirmed' => '1',
        'locale' => 'de',
    ])->assertRedirect()->assertSessionHas('status', 'login-link-sent');

    Mail::assertSent(LoginLinkMail::class, fn (LoginLinkMail $mail): bool => $mail->hasTo('Gary@Example.com')
        && $mail->locale === 'de'
        && str_contains($mail->url, '/login/'));

    $link = LoginLink::sole();
    expect($link->email)->toBe('gary@example.com')
        ->and($link->locale)->toBe('de')
        ->and($link->token_hash)->toHaveLength(64)
        ->and($link->expires_at->isFuture())->toBeTrue();
});

it('requires the age confirmation', function () {
    $this->post(route('login-link.store'), [
        'email' => 'kid@example.com',
        'locale' => 'en',
    ])->assertSessionHasErrors(['age_confirmed' => 'You must be at least 16 years old to play.']);

    Mail::assertNothingSent();
});

it('rejects invalid e-mail addresses and unsupported languages', function () {
    $this->post(route('login-link.store'), [
        'email' => 'not-an-email',
        'age_confirmed' => '1',
        'locale' => 'xx',
    ])->assertSessionHasErrors(['email', 'locale']);

    Mail::assertNothingSent();
});

it('throttles repeated requests for the same address', function () {
    $payload = ['email' => 'spam@example.com', 'age_confirmed' => '1', 'locale' => 'en'];

    foreach (range(1, 3) as $attempt) {
        $this->post(route('login-link.store'), $payload)->assertRedirect();
    }

    $this->post(route('login-link.store'), $payload)->assertTooManyRequests();
});

it('shows validation messages in the requested language', function () {
    $this->withHeader('Accept-Language', 'de')
        ->post(route('login-link.store'), ['email' => 'kid@example.com', 'locale' => 'de'])
        ->assertSessionHasErrors(['age_confirmed' => 'Du musst mindestens 16 Jahre alt sein, um zu spielen.']);
});
