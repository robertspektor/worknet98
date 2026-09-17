<?php

use App\Career\ReviewRating;
use App\Cases\Metric;
use App\Cases\MetricBook;
use App\Models\Email;
use App\Models\Employment;
use App\Models\LedgerEntry;
use App\Models\PerformanceReview;
use App\Work\LedgerReason;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CompanySeeder;

beforeEach(function () {
    $this->travelTo('2026-09-21 09:00:00');
    $this->seed([CompanySeeder::class, BranchSeeder::class]);
    $this->employment = employAtSeededPosition('flowright-plumbing');
});

function earn(Employment $employment, array $effects): void
{
    app(MetricBook::class)->apply($employment, $effects);
}

it('does not review a month before it has ended', function () {
    earn($this->employment, [Metric::CustomerSatisfaction->value => 2]);
    $this->travelTo('2026-09-30 23:59:59');

    $this->artisan('game:tick')->assertSuccessful();

    expect(PerformanceReview::count())->toBe(0);
});

it('rewards an excellent month with a bonus and praise from the superior', function () {
    earn($this->employment, [Metric::CustomerSatisfaction->value => 2, Metric::Punctuality->value => 1]);
    $this->travelTo('2026-10-01 00:05:00');

    $this->artisan('game:tick')->assertSuccessful();

    $review = PerformanceReview::sole();
    expect($review->period)->toBe('2026-09')
        ->and($review->rating)->toBe(ReviewRating::Excellent)
        ->and($review->score)->toBe(3)
        ->and($review->bonus)->toBe(300)
        ->and(LedgerEntry::query()->where('reason', LedgerReason::Bonus)->sole()->amount)->toBe(300);

    $mail = Email::query()->where('subject', 'Your review for September 2026')->sole();
    expect($mail->sender_name)->toBe('Gary Flowright')
        ->and($mail->body)->toContain('Customer satisfaction: +2')
        ->and($mail->body)->toContain('Punctuality: +1')
        ->and($mail->body)->toContain('300 credits');
});

it('gives a warning for a poor month', function () {
    earn($this->employment, [Metric::Cost->value => -2, Metric::Reliability->value => -1]);
    $this->travelTo('2026-10-01 00:05:00');

    $this->artisan('game:tick')->assertSuccessful();

    expect(PerformanceReview::sole()->rating)->toBe(ReviewRating::Poor)
        ->and(LedgerEntry::query()->where('reason', LedgerReason::Bonus)->exists())->toBeFalse()
        ->and(Email::query()->where('subject', 'Your review for September 2026')->sole()->body)->toContain('official warning');
});

it('reviews every month only once and counts only what changed since the last review', function () {
    earn($this->employment, [Metric::CustomerSatisfaction->value => 3]);
    $this->travelTo('2026-10-01 00:05:00');
    $this->artisan('game:tick')->assertSuccessful();
    $this->artisan('game:tick')->assertSuccessful();

    earn($this->employment, [Metric::Punctuality->value => 1]);
    $this->travelTo('2026-11-01 00:05:00');
    $this->artisan('game:tick')->assertSuccessful();

    expect(PerformanceReview::query()->orderBy('period')->pluck('score', 'period')->all())->toBe(['2026-09' => 3, '2026-10' => 1])
        ->and(PerformanceReview::query()->where('period', '2026-10')->sole()->rating)->toBe(ReviewRating::Solid);
});

it('writes the review in the language of the company', function () {
    $employment = employAtSeededPosition('rohr-und-sohn');
    earn($employment, [Metric::CustomerSatisfaction->value => 1]);
    $this->travelTo('2026-10-01 00:05:00');

    $this->artisan('game:tick')->assertSuccessful();

    expect(Email::query()->where('subject', 'Deine Beurteilung für September 2026')->sole()->body)->toContain('Kundenzufriedenheit: +1');
});

it('does not review a month in which the player was not employed yet', function () {
    $this->travelTo('2026-10-01 00:05:00');
    employAtSeededPosition('flowright-plumbing', 'office-assistant-2');

    $this->artisan('game:tick')->assertSuccessful();

    expect(PerformanceReview::query()->pluck('employment_id')->all())->toBe([$this->employment->id]);
});
