<?php

use App\Career\Promotions\PromotionOfferStatus;
use App\Cases\Metric;
use App\Cases\MetricBook;
use App\Cases\WorkCaseKind;
use App\Cases\WorkCaseStatus;
use App\Models\Appointment;
use App\Models\Email;
use App\Models\Employment;
use App\Models\Position;
use App\Models\PromotionOffer;
use App\Models\User;
use App\Models\WorkCase;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CompanySeeder;

beforeEach(function () {
    $this->travelTo('2026-06-08 09:00:00');
    $this->seed([CompanySeeder::class, CitySeeder::class, BranchSeeder::class]);
    $this->employment = employAtSeededPosition('flowright-plumbing');
    $this->player = User::findOrFail($this->employment->user_id);
});

function closeMonthUntil(string $nextMonthStart, array $effects): void
{
    app(MetricBook::class)->apply(test()->employment, $effects);
    test()->travelTo("{$nextMonthStart} 00:05:00");
    test()->artisan('game:tick')->assertSuccessful();
}

function excellentMonthUntil(string $nextMonthStart): void
{
    closeMonthUntil($nextMonthStart, [Metric::CustomerSatisfaction->value => 2, Metric::Punctuality->value => 1]);
}

function poorMonthUntil(string $nextMonthStart): void
{
    closeMonthUntil($nextMonthStart, [Metric::Cost->value => -2]);
}

function earnPromotionOffer(): PromotionOffer
{
    excellentMonthUntil('2026-07-01');
    excellentMonthUntil('2026-08-01');
    excellentMonthUntil('2026-09-01');

    return PromotionOffer::query()->sole();
}

function seededPosition(string $slug): Position
{
    return Position::query()->whereRelation('branch.company', 'slug', 'flowright-plumbing')->where('slug', $slug)->sole();
}

it('does not offer a promotion before three excellent months', function () {
    excellentMonthUntil('2026-07-01');
    excellentMonthUntil('2026-08-01');

    expect(PromotionOffer::count())->toBe(0);
});

it('offers the next positions after three excellent months', function () {
    $offer = earnPromotionOffer();

    expect($offer->status)->toBe(PromotionOfferStatus::Pending)
        ->and($offer->options->pluck('position.title')->all())->toBe(['Emergency Dispatcher', 'Service Contract Planner'])
        ->and($offer->options->pluck('daily_salary')->all())->toBe([130, 120]);

    $mail = Email::query()->where('subject', "Let's talk about your future")->sole();
    expect($mail->id)->toBe($offer->email_id)
        ->and($mail->employment_id)->toBe($this->employment->id)
        ->and($mail->sender_name)->toBe('Gary Flowright')
        ->and($mail->body)->toContain('- Emergency Dispatcher, 130 credits per day')
        ->and($mail->body)->toContain('Right now you make 100 credits per day')
        ->and($mail->body)->toContain('until the end of September 2026');
});

it('does not offer a promotion while a recent month was poor', function () {
    excellentMonthUntil('2026-07-01');
    excellentMonthUntil('2026-08-01');
    poorMonthUntil('2026-09-01');
    excellentMonthUntil('2026-10-01');

    expect(PromotionOffer::count())->toBe(0);

    excellentMonthUntil('2026-11-01');

    expect(PromotionOffer::count())->toBe(1);
});

it('shows the offer with the mail in the work mailbox', function () {
    $offer = earnPromotionOffer();

    $this->actingAs($this->player)
        ->getJson(route('api.v1.emails.index', ['mailbox' => 'work']))
        ->assertJsonPath('data.0.promotion_offer.id', $offer->id)
        ->assertJsonPath('data.0.promotion_offer.status', 'pending')
        ->assertJsonPath('data.0.promotion_offer.options.0.title', 'Emergency Dispatcher')
        ->assertJsonPath('data.0.promotion_offer.options.0.daily_salary', 130);
});

it('promotes the player into the chosen position', function () {
    $offer = earnPromotionOffer();
    $target = seededPosition('emergency-dispatcher');
    $previous = Position::findOrFail($this->employment->position_id);

    $this->actingAs($this->player)
        ->postJson(route('api.v1.promotion-offers.acceptance.store', $offer), ['position_id' => $target->id])
        ->assertSuccessful()
        ->assertJsonPath('data.status', 'accepted')
        ->assertJsonPath('data.accepted_position_id', $target->id);

    $employment = $this->employment->fresh();
    expect($employment->position_id)->toBe($target->id)
        ->and($employment->daily_salary)->toBe(130)
        ->and($target->isHeldByPlayer())->toBeTrue()
        ->and($previous->isHeldByPlayer())->toBeFalse();

    $letter = Email::query()->where('subject', 'Welcome to your new desk')->sole();
    expect($letter->employment_id)->toBe($employment->id)
        ->and($letter->body)->toContain('you are our new Emergency Dispatcher')
        ->and($letter->body)->toContain('130 credits per day')
        ->and($letter->body)->toContain('Tony Russo has moved on to headquarters');

    $this->actingAs($this->player)->get(route('office'))
        ->assertInertia(fn ($page) => $page->where('workplace.jobTitle', 'Emergency Dispatcher'));
});

it('hands unbooked cases to colleagues and keeps booked ones on promotion', function () {
    $offer = earnPromotionOffer();
    foreach (['priya-raman', 'walter-beck'] as $customer) {
        $this->artisan('cases:open', ['company' => 'flowright-plumbing', 'branch' => 'maple-falls', 'template' => 'leaking-pipe', '--customer' => $customer])->assertSuccessful();
    }
    WorkCase::query()->update(['position_id' => $this->employment->position_id, 'employment_id' => $this->employment->id]);
    [$unbooked, $booked] = WorkCase::query()->orderBy('id')->get()->all();
    Appointment::factory()->create([
        'branch_id' => $booked->branch_id,
        'customer_id' => $booked->customer_id,
        'booked_by_employment_id' => $this->employment->id,
    ]);

    $this->actingAs($this->player)
        ->postJson(route('api.v1.promotion-offers.acceptance.store', $offer), ['position_id' => seededPosition('service-contract-planner')->id])
        ->assertSuccessful();

    expect($unbooked->fresh()->employment_id)->toBeNull()
        ->and($unbooked->fresh()->position->slug)->not->toBe('office-assistant-1')
        ->and($booked->fresh()->employment_id)->toBe($this->employment->id);
});

it('keeps an unbooked scripted onboarding case with the promoted player', function () {
    $offer = earnPromotionOffer();
    $this->artisan('cases:open', ['company' => 'flowright-plumbing', 'branch' => 'maple-falls', 'template' => 'leaking-pipe', '--customer' => 'margaret-hollis'])->assertSuccessful();
    $onboarding = WorkCase::query()->sole();
    $onboarding->update([
        'kind' => WorkCaseKind::Scripted,
        'case_slug' => 'hollis-leaking-sink',
        'position_id' => $this->employment->position_id,
        'employment_id' => $this->employment->id,
    ]);

    $this->actingAs($this->player)
        ->postJson(route('api.v1.promotion-offers.acceptance.store', $offer), ['position_id' => seededPosition('emergency-dispatcher')->id])
        ->assertSuccessful();

    expect($onboarding->fresh()->status)->toBe(WorkCaseStatus::Open)
        ->and($onboarding->fresh()->employment_id)->toBe($this->employment->id);
});

it('routes cases of the new responsibility to the promoted player', function () {
    $offer = earnPromotionOffer();
    $this->actingAs($this->player)
        ->postJson(route('api.v1.promotion-offers.acceptance.store', $offer), ['position_id' => seededPosition('emergency-dispatcher')->id])
        ->assertSuccessful();

    $this->artisan('cases:open', ['company' => 'flowright-plumbing', 'branch' => 'maple-falls', 'template' => 'burst-pipe', '--customer' => 'priya-raman'])->assertSuccessful();

    expect(WorkCase::query()->latest('id')->firstOrFail()->employment_id)->toBe($this->employment->id)
        ->and(Email::query()->where('subject', 'EMERGENCY: burst pipe')->sole()->user_id)->toBe($this->player->id);
});

it('lets the player decline and waits for three new excellent months', function () {
    $offer = earnPromotionOffer();

    $this->actingAs($this->player)
        ->postJson(route('api.v1.promotion-offers.decline.store', $offer))
        ->assertSuccessful()
        ->assertJsonPath('data.status', 'declined');

    excellentMonthUntil('2026-10-01');
    excellentMonthUntil('2026-11-01');
    expect(PromotionOffer::count())->toBe(1);

    excellentMonthUntil('2026-12-01');
    expect(PromotionOffer::count())->toBe(2);
});

it('lets an offer expire at the next month close', function () {
    $offer = earnPromotionOffer();
    $this->travelTo('2026-10-01 00:05:00');

    $this->actingAs($this->player)
        ->postJson(route('api.v1.promotion-offers.acceptance.store', $offer), ['position_id' => seededPosition('emergency-dispatcher')->id])
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'offer_closed');

    $this->actingAs($this->player)
        ->getJson(route('api.v1.emails.index', ['mailbox' => 'work']))
        ->assertJsonPath('data.0.promotion_offer.status', 'expired');
});

it('refuses a position that was not offered', function () {
    $offer = earnPromotionOffer();

    $this->actingAs($this->player)
        ->postJson(route('api.v1.promotion-offers.acceptance.store', $offer), ['position_id' => seededPosition('branch-manager')->id])
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'position_not_offered');
});

it('refuses a position another player took in the meantime', function () {
    $offer = earnPromotionOffer();
    $target = seededPosition('emergency-dispatcher');
    Employment::factory()->at($target)->create();

    $this->actingAs($this->player)
        ->postJson(route('api.v1.promotion-offers.acceptance.store', $offer), ['position_id' => $target->id])
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'position_taken');
});

it('only offers positions no other player holds', function () {
    Employment::factory()->at(seededPosition('emergency-dispatcher'))->create();

    $offer = earnPromotionOffer();

    expect($offer->options->pluck('position.slug')->all())->toBe(['service-contract-planner']);
});

it('does not let another player answer the offer', function () {
    $offer = earnPromotionOffer();

    $this->actingAs(User::factory()->create())
        ->postJson(route('api.v1.promotion-offers.decline.store', $offer))
        ->assertForbidden();
});

it('starts the warnings from zero in the new position', function () {
    poorMonthUntil('2026-07-01');
    excellentMonthUntil('2026-08-01');
    excellentMonthUntil('2026-09-01');
    excellentMonthUntil('2026-10-01');
    $this->actingAs($this->player)
        ->postJson(route('api.v1.promotion-offers.acceptance.store', PromotionOffer::query()->sole()), ['position_id' => seededPosition('emergency-dispatcher')->id])
        ->assertSuccessful();

    poorMonthUntil('2026-11-01');

    expect(Email::query()->where('subject', 'Your review for October 2026')->sole()->body)->toContain('Official warnings in this job: 1 of 3');
});
