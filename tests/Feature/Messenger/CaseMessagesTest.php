<?php

use App\Models\ChatMessage;
use App\Models\Customer;
use App\Models\User;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CompanySeeder;

beforeEach(function () {
    $this->travelTo('2026-09-21 09:00:00');
    $this->seed([CompanySeeder::class, BranchSeeder::class]);
    $employment = employAtSeededPosition('flowright-plumbing');
    $this->player = User::findOrFail($employment->user_id);
    $this->hollis = Customer::query()->where('slug', 'margaret-hollis')->sole();
});

function bookForHollis(User $player, Customer $hollis, string $technician): void
{
    workAs($player, 'POST', 'api.v1.appointments.store', ['customer_id' => $hollis->id, 'technician_id' => technician($technician)->id, 'date' => '2026-09-24', 'slot' => '08:00']);
}

it('sends the colleague warning shortly after the case opens', function () {
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');

    $this->actingAs($this->player)->getJson(route('api.v1.chat-messages.index'))->assertJsonCount(0, 'data');

    $this->travel(30)->seconds();

    $this->actingAs($this->player)
        ->getJson(route('api.v1.chat-messages.index'))
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.contact_name', 'Bev Mercer')
        ->assertJsonPath('data.0.is_from_player', false)
        ->assertJsonPath('data.0.replies.*.slug', ['thanks', 'ladder']);
});

it('stays quiet when a player holds the position of the colleague', function () {
    employAtSeededPosition('flowright-plumbing', 'office-coordinator');
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');

    $this->travel(90)->seconds();

    $this->actingAs($this->player)->getJson(route('api.v1.chat-messages.index'))->assertJsonCount(0, 'data');
});

it('interrupts when Stan is booked for Mrs. Hollis', function () {
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');

    bookForHollis($this->player, $this->hollis, 'stan-kowalski');

    expect(ChatMessage::query()->where('message_slug', 'stan-booked')->sole()->body)->toContain('STAN');
});

it('stays quiet when a different technician is booked', function () {
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');

    bookForHollis($this->player, $this->hollis, 'rita-vance');

    expect(ChatMessage::query()->where('message_slug', 'stan-booked')->exists())->toBeFalse();
});

it('sends every case message only once', function () {
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');
    bookForHollis($this->player, $this->hollis, 'stan-kowalski');
    $this->actingAs($this->player)->deleteJson(route('api.v1.appointments.destroy', $this->player->employment?->bookedAppointments()->sole()))->assertNoContent();

    bookForHollis($this->player, $this->hollis, 'stan-kowalski');

    expect(ChatMessage::query()->where('message_slug', 'stan-booked')->count())->toBe(1);
});
