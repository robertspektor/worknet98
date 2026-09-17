<?php

use App\Models\Email;
use App\Models\ForumPost;
use App\Models\ForumThread;
use App\Models\Position;
use App\Models\User;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\ForumSeeder;

beforeEach(function () {
    $this->travelTo('2026-09-21 09:00:00');
    $this->seed([CompanySeeder::class, CitySeeder::class, BranchSeeder::class, ForumSeeder::class]);
    $this->player = User::findOrFail(employAtSeededPosition('rohr-und-sohn')->user_id);
});

it('seeds the company board with threads written by colleagues', function () {
    $this->actingAs($this->player)->getJson(route('api.v1.forum.threads.index'))
        ->assertOk()
        ->assertJsonFragment(['title' => 'Kaffeekasse', 'author' => 'Bettina Mertens', 'posts_count' => 3]);

    $thread = ForumThread::query()->where('slug', 'kaffeekasse')->sole();

    $this->actingAs($this->player)->getJson(route('api.v1.forum.threads.posts.index', $thread))
        ->assertOk()
        ->assertJsonPath('data.0.author', 'Bettina Mertens')
        ->assertJsonPath('data.1.author', 'Kerstin Albers')
        ->assertJsonPath('data.0.is_own', false);
});

it('seeds the board idempotently', function () {
    $counts = [ForumThread::count(), ForumPost::count()];

    $this->seed(ForumSeeder::class);

    expect([ForumThread::count(), ForumPost::count()])->toBe($counts);
});

it('lets an employee start a thread and colleagues reply', function () {
    workAs($this->player, 'POST', 'api.v1.forum.threads.store', ['title' => 'Der Drucker', 'body' => 'Der Drucker im Flur frisst wieder Papier.']);

    $thread = ForumThread::query()->where('title', 'Der Drucker')->sole();
    expect($thread->company->slug)->toBe('rohr-und-sohn')
        ->and($thread->posts()->count())->toBe(1);

    $this->travel(11)->seconds();
    $colleague = User::findOrFail(employAtSeededPosition('rohr-und-sohn', 'office-assistant-2')->user_id);
    $this->actingAs($colleague)
        ->postJson(route('api.v1.forum.threads.posts.store', $thread), ['body' => 'Er frisst nur Papier, das schon bedruckt ist.'])
        ->assertCreated();

    $this->actingAs($this->player)->getJson(route('api.v1.forum.threads.posts.index', $thread))
        ->assertOk()
        ->assertJsonPath('data.0.is_own', true)
        ->assertJsonPath('data.1.is_own', false)
        ->assertJsonPath('data.1.author_title', 'Bürokraft Terminplanung');
});

it('slows down players who post too fast', function () {
    workAs($this->player, 'POST', 'api.v1.forum.threads.store', ['title' => 'Erste Nachricht', 'body' => 'Bitte um Aufmerksamkeit.']);

    $this->actingAs($this->player)
        ->postJson(route('api.v1.forum.threads.store'), ['title' => 'Zweite Nachricht', 'body' => 'Immer noch ich.'])
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'too_fast');
});

it('keeps the board of other companies out of reach', function () {
    $otherThread = ForumThread::query()->whereRelation('company', 'slug', 'flowright-plumbing')->firstOrFail();

    $this->actingAs($this->player)->getJson(route('api.v1.forum.threads.posts.index', $otherThread))->assertForbidden();

    $this->actingAs($this->player)->getJson(route('api.v1.forum.threads.index'))
        ->assertOk()
        ->assertJsonMissing(['title' => 'Coffee fund']);
});

it('lets players delete only their own posts', function () {
    workAs($this->player, 'POST', 'api.v1.forum.threads.store', ['title' => 'Versehen', 'body' => 'Das wollte ich nicht abschicken.']);
    $own = ForumPost::query()->whereNotNull('author_employment_id')->sole();
    $seeded = ForumPost::query()->whereNull('author_employment_id')->firstOrFail();

    $this->actingAs($this->player)->deleteJson(route('api.v1.forum.posts.destroy', $seeded))->assertForbidden();
    $this->actingAs($this->player)->deleteJson(route('api.v1.forum.posts.destroy', $own))->assertNoContent();

    expect(ForumPost::query()->whereKey($own->id)->exists())->toBeFalse();
});

it('sends mail to a colleague of the own branch', function () {
    $colleagueEmployment = employAtSeededPosition('rohr-und-sohn', 'office-assistant-2');
    $colleague = User::findOrFail($colleagueEmployment->user_id);
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');

    workAs($this->player, 'POST', 'api.v1.emails.store', [
        'colleague_position_id' => $colleagueEmployment->position_id,
        'subject' => 'Mittag?',
        'body' => 'Kantine oder Bäcker?',
        'action' => 'other',
    ]);

    $received = Email::query()->where('user_id', $colleague->id)->sole();
    expect($received->subject)->toBe('Mittag?')
        ->and($received->sender_name)->toBe('Uwe Brandt')
        ->and($received->sender_address)->toBe('uwe.brandt@rohrundsohn.wn')
        ->and(Email::query()->where('user_id', $this->player->id)->where('folder', 'sent')->sole()->recipient_name)->toBe('Sabine Kröger');
});

it('lists the colleagues of the own branch as possible recipients', function () {
    $this->actingAs($this->player)->getJson(route('api.v1.colleagues.index'))
        ->assertOk()
        ->assertJsonFragment(['name' => 'Bernd Rohr', 'title' => 'Filialleitung'])
        ->assertJsonMissing(['name' => 'Uwe Brandt']);
});

it('refuses mail to positions of other branches', function () {
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');
    $stranger = Position::query()->whereRelation('branch.company', 'slug', 'nordwerk-logistik')->firstOrFail();

    $this->actingAs($this->player)
        ->postJson(route('api.v1.emails.store'), ['colleague_position_id' => $stranger->id, 'subject' => 'Hallo', 'body' => 'Fremde Firma.', 'action' => 'other'])
        ->assertUnprocessable();
});
