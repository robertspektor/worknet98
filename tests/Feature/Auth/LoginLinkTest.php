<?php

use App\Mail\LoginLinkMail;
use App\Models\LoginLink;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(fn () => Mail::fake());

it('sends a sign-in link to an address that is registered', function () {
    User::factory()->locale('en')->create(['email' => 'gary@example.com']);

    $this->post(route('login-link.store'), [
        'email' => 'GARY@example.com',
        'locale' => 'de',
    ])->assertRedirect()->assertSessionHas('status', 'login-link-sent');

    $this->assertGuest();
    Mail::assertSent(LoginLinkMail::class, fn (LoginLinkMail $mail): bool => $mail->hasTo('GARY@example.com'));
    expect(LoginLink::sole()->email)->toBe('gary@example.com');
});

/* An unknown address must not be able to create a player through the link,
   and the terminal must not tell the visitor which addresses exist. */

it('says the same thing for an address nobody is registered with', function () {
    $this->post(route('login-link.store'), [
        'email' => 'stranger@example.com',
        'locale' => 'en',
    ])->assertRedirect()->assertSessionHas('status', 'login-link-sent');

    Mail::assertNothingSent();
    expect(LoginLink::count())->toBe(0)
        ->and(User::count())->toBe(0);
});

it('rejects invalid addresses and unsupported languages', function () {
    $this->post(route('login-link.store'), ['email' => 'not-an-email', 'locale' => 'xx'])
        ->assertSessionHasErrors(['email', 'locale']);

    Mail::assertNothingSent();
});
