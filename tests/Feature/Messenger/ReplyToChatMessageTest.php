<?php

use App\Models\ChatMessage;
use App\Models\Company;
use App\Models\Shift;
use Illuminate\Testing\TestResponse;

beforeEach(function () {
    $this->travelTo('2026-09-21 09:00:00');
    $this->player = playerOnDutyAt(Company::factory()->create());
    $this->message = ChatMessage::factory()->create([
        'employment_id' => $this->player->employment?->id,
        'contact_name' => 'Bev Mercer',
        'body' => 'psst. do not send Stan.',
        'replies' => [
            ['slug' => 'thanks', 'text' => 'Thanks for the heads-up!', 'answer' => 'np.'],
            ['slug' => 'ladder', 'text' => 'What happened with the ladder?', 'answer' => 'we do not talk about the ladder.'],
        ],
    ]);
});

function replyTo(ChatMessage $message, string $reply): TestResponse
{
    return test()->postJson(route('api.v1.chat-messages.replies.store', $message), ['reply' => $reply]);
}

it('replies with a canned reply and gets an answer a moment later', function () {
    $this->actingAs($this->player);

    replyTo($this->message, 'ladder')
        ->assertCreated()
        ->assertJsonPath('data.is_from_player', true)
        ->assertJsonPath('data.body', 'What happened with the ladder?');

    $this->getJson(route('api.v1.chat-messages.index'))
        ->assertJsonPath('data.*.body', ['psst. do not send Stan.', 'What happened with the ladder?'])
        ->assertJsonPath('data.0.replies', []);

    $this->travel(5)->seconds();

    $this->getJson(route('api.v1.chat-messages.index'))
        ->assertJsonPath('data.2.body', 'we do not talk about the ladder.')
        ->assertJsonPath('data.2.contact_name', 'Bev Mercer');
});

it('refuses a second reply to the same message', function () {
    $this->actingAs($this->player);
    replyTo($this->message, 'thanks')->assertCreated();

    replyTo($this->message, 'ladder')->assertUnprocessable()->assertJsonPath('refusal', 'already_replied');
});

it('refuses replies that the message does not offer', function () {
    $this->actingAs($this->player);

    replyTo($this->message, 'quit-my-job')->assertUnprocessable()->assertJsonPath('refusal', 'unknown_reply');
});

it('refuses replies while not on duty', function () {
    Shift::query()->update(['clocked_out_at' => now()]);
    $this->actingAs($this->player);

    replyTo($this->message, 'thanks')->assertUnprocessable()->assertJsonPath('refusal', 'not_on_duty');
});

it('does not let players reply to messages of other players', function () {
    $this->actingAs(playerOnDutyAt(Company::factory()->create()));

    replyTo($this->message, 'thanks')->assertForbidden();
});

it('only lists messages of the current employment', function () {
    ChatMessage::factory()->create(['body' => 'Someone else']);

    $this->actingAs($this->player)
        ->getJson(route('api.v1.chat-messages.index'))
        ->assertJsonPath('data.*.body', ['psst. do not send Stan.']);
});

it('marks all messages as read', function () {
    $this->actingAs($this->player)->getJson(route('api.v1.chat-messages.index'))->assertJsonPath('data.0.is_read', false);

    $this->actingAs($this->player)->postJson(route('api.v1.chat-messages.read.store'))->assertNoContent();

    $this->actingAs($this->player)->getJson(route('api.v1.chat-messages.index'))->assertJsonPath('data.0.is_read', true);
});
