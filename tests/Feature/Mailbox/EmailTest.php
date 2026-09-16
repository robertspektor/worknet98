<?php

use App\Models\Email;
use App\Models\User;

it('lists the emails of the player newest first', function () {
    $player = User::factory()->create();
    $older = Email::factory()->for($player)->read()->create(['received_at' => now()->subHour(), 'subject' => 'Older']);
    $newer = Email::factory()->for($player)->create(['received_at' => now(), 'subject' => 'Newer']);
    Email::factory()->create();

    $this->actingAs($player)
        ->getJson(route('api.v1.emails.index'))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.id', $newer->id)
        ->assertJsonPath('data.0.is_read', false)
        ->assertJsonPath('data.1.id', $older->id)
        ->assertJsonPath('data.1.is_read', true);
});

it('marks an email as read', function () {
    $this->freezeSecond();
    $email = Email::factory()->create();

    $this->actingAs($email->user)
        ->postJson(route('api.v1.emails.read.store', $email))
        ->assertNoContent();

    expect($email->fresh()?->read_at?->equalTo(now()))->toBeTrue();
});

it('keeps the first reading time', function () {
    $email = Email::factory()->create(['read_at' => now()->subDay()]);

    $this->actingAs($email->user)->postJson(route('api.v1.emails.read.store', $email))->assertNoContent();

    expect($email->fresh()?->read_at?->equalTo(now()->subDay()))->toBeTrue();
})->freezeSecond();

it('does not let players read the emails of others', function () {
    $email = Email::factory()->create();

    $this->actingAs(User::factory()->create())
        ->postJson(route('api.v1.emails.read.store', $email))
        ->assertForbidden();

    expect($email->fresh()?->read_at)->toBeNull();
});

it('requires a signed-in player to read emails', function () {
    $this->getJson(route('api.v1.emails.index'))->assertUnauthorized();
});
