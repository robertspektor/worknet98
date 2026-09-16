<?php

use App\Models\User;
use Illuminate\Support\Collection;
use Inertia\Testing\AssertableInertia;

it('uses the browser language for guests', function () {
    $this->withHeader('Accept-Language', 'de-DE,de;q=0.9,en;q=0.8')
        ->get(route('home'))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('computer')
            ->where('locale', 'de')
            ->where('translations', fn (Collection $translations): bool => $translations->get('desktop.recycle_bin') === 'Papierkorb'));
});

it('falls back to English for unsupported browser languages', function () {
    $this->withHeader('Accept-Language', 'fr-FR')
        ->get(route('home'))
        ->assertInertia(fn (AssertableInertia $page) => $page->where('locale', 'en'));
});

it('prefers the player language over the browser language', function () {
    $this->actingAs(User::factory()->locale('de')->create())
        ->withHeader('Accept-Language', 'en')
        ->get(route('home'))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('locale', 'de')
            ->where('player.locale', 'de'));
});

it('lets guests switch the language for their session', function () {
    $this->put(route('locale.update'), ['locale' => 'de'])->assertRedirect();

    $this->get(route('home'))
        ->assertInertia(fn (AssertableInertia $page) => $page->where('locale', 'de'));
});

it('stores the language on the player', function () {
    $player = User::factory()->locale('en')->create();

    $this->actingAs($player)->put(route('locale.update'), ['locale' => 'de'])->assertRedirect();

    expect($player->fresh()?->locale)->toBe('de');
});

it('rejects unsupported languages', function () {
    $this->put(route('locale.update'), ['locale' => 'tlh'])->assertSessionHasErrors('locale');
});
