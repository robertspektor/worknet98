<?php

use App\Models\DeskPlacement;
use App\Models\User;

beforeEach(fn () => $this->player = User::factory()->create());

it('remembers where the player put an item', function () {
    $this->actingAs($this->player)
        ->postJson(route('api.v1.desk-placements.store'), ['item' => 'employee-badge', 'x' => 0.25, 'y' => 0.88])
        ->assertSuccessful()
        ->assertJsonPath('data.item', 'employee-badge');

    $this->actingAs($this->player)
        ->getJson(route('api.v1.desk-placements.index'))
        ->assertJsonPath('data.0.x', 0.25)
        ->assertJsonPath('data.0.y', 0.88);
});

it('moves an item instead of placing it twice', function () {
    foreach ([0.2, 0.7] as $x) {
        $this->actingAs($this->player)
            ->postJson(route('api.v1.desk-placements.store'), ['item' => 'floppy-box', 'x' => $x, 'y' => 0.5])
            ->assertSuccessful();
    }

    expect(DeskPlacement::query()->where('user_id', $this->player->id)->get())->toHaveCount(1)
        ->and(DeskPlacement::sole()->x)->toBe(0.7);
});

it('refuses a spot outside the room and an unknown item name', function () {
    $this->actingAs($this->player)
        ->postJson(route('api.v1.desk-placements.store'), ['item' => 'floppy-box', 'x' => 1.4, 'y' => 0.5])
        ->assertJsonValidationErrorFor('x');

    $this->actingAs($this->player)
        ->postJson(route('api.v1.desk-placements.store'), ['item' => 'DROP TABLE', 'x' => 0.5, 'y' => 0.5])
        ->assertJsonValidationErrorFor('item');
});

it('only shows the placements of the player', function () {
    DeskPlacement::query()->create(['user_id' => User::factory()->create()->id, 'item' => 'mug', 'x' => 0.1, 'y' => 0.1]);

    $this->actingAs($this->player)
        ->getJson(route('api.v1.desk-placements.index'))
        ->assertJsonCount(0, 'data');
});
