<?php

use App\Http\Requests\UpdateNoteRequest;
use App\Models\User;

it('starts with an empty note', function () {
    $this->actingAs(User::factory()->create())
        ->getJson(route('api.v1.note.show'))
        ->assertOk()
        ->assertExactJson(['data' => ['body' => '']]);
});

it('saves the note of the player', function () {
    $player = User::factory()->create();

    $this->actingAs($player)->putJson(route('api.v1.note.update'), ['body' => "Technician Dave: Tuesdays\n"])->assertNoContent();
    $this->actingAs($player)->putJson(route('api.v1.note.update'), ['body' => "Technician Dave: Wednesdays\n"])->assertNoContent();

    $this->actingAs($player)
        ->getJson(route('api.v1.note.show'))
        ->assertExactJson(['data' => ['body' => "Technician Dave: Wednesdays\n"]]);
    $this->actingAs(User::factory()->create())
        ->getJson(route('api.v1.note.show'))
        ->assertExactJson(['data' => ['body' => '']]);
});

it('saves an emptied note', function () {
    $player = User::factory()->create();
    $this->actingAs($player)->putJson(route('api.v1.note.update'), ['body' => 'Buy milk'])->assertNoContent();

    $this->actingAs($player)->putJson(route('api.v1.note.update'), ['body' => ''])->assertNoContent();

    $this->actingAs($player)->getJson(route('api.v1.note.show'))->assertExactJson(['data' => ['body' => '']]);
});

it('limits the note length', function () {
    $this->actingAs(User::factory()->create())
        ->putJson(route('api.v1.note.update'), ['body' => str_repeat('a', UpdateNoteRequest::BODY_MAX_LENGTH + 1)])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('body');
});

it('requires a signed-in player for the note', function () {
    $this->getJson(route('api.v1.note.show'))->assertUnauthorized();
});
