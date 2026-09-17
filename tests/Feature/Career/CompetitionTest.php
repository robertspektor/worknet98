<?php

use App\Career\ReviewRating;
use App\Cases\Metric;
use App\Cases\MetricBook;
use App\Models\EmployeeAward;
use App\Models\Employment;
use App\Models\ForumThread;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\PerformanceReview;
use App\Models\Position;
use App\Models\User;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\ForumSeeder;

beforeEach(function () {
    $this->travelTo('2026-09-21 09:00:00');
    $this->seed([CompanySeeder::class, CitySeeder::class, BranchSeeder::class, ForumSeeder::class]);
});

function worksWell(Employment $employment, int $satisfaction): void
{
    app(MetricBook::class)->apply($employment, [Metric::CustomerSatisfaction->value => $satisfaction]);
}

it('awards the best reviewed employee of a branch and puts it on the notice board', function () {
    $winner = employAtSeededPosition('rohr-und-sohn', 'office-assistant-1');
    $runnerUp = employAtSeededPosition('rohr-und-sohn', 'office-assistant-2');
    worksWell($winner, 3);
    worksWell($runnerUp, 1);

    $this->travelTo('2026-10-01 00:05:00');
    $this->artisan('game:tick')->assertSuccessful();

    $award = EmployeeAward::query()->sole();
    expect($award->employment_id)->toBe($winner->id)
        ->and($award->period)->toBe('2026-09')
        ->and(ForumThread::query()->where('title', 'Mitarbeiter des Monats 2026-09')->sole()->posts()->sole()->body)
        ->toContain('Uwe Brandt');
});

it('awards each month only once and nobody with a bad month', function () {
    $employment = employAtSeededPosition('rohr-und-sohn', 'office-assistant-1');
    worksWell($employment, -2);

    $this->travelTo('2026-10-01 00:05:00');
    $this->artisan('game:tick')->assertSuccessful();
    $this->artisan('game:tick')->assertSuccessful();

    expect(EmployeeAward::count())->toBe(0)
        ->and(PerformanceReview::count())->toBe(1);
});

it('ranks employees by their reviews for the branch and for the whole world', function () {
    $strong = employAtSeededPosition('rohr-und-sohn', 'office-assistant-1');
    $weak = employAtSeededPosition('rohr-und-sohn', 'office-assistant-2');
    $abroad = employAtSeededPosition('flowright-plumbing', 'office-assistant-1');
    worksWell($strong, 3);
    worksWell($weak, 1);
    worksWell($abroad, 2);

    $this->travelTo('2026-10-01 00:05:00');
    $this->artisan('game:tick')->assertSuccessful();

    $player = User::findOrFail($strong->user_id);
    $this->actingAs($player)->getJson(route('api.v1.rankings.show'))
        ->assertOk()
        ->assertJsonPath('data.branch.0.name', 'Uwe Brandt')
        ->assertJsonPath('data.branch.0.is_own', true)
        ->assertJsonPath('data.branch.1.name', 'Sabine Kröger')
        ->assertJsonCount(2, 'data.branch')
        ->assertJsonCount(3, 'data.world')
        ->assertJsonPath('data.world.1.company', 'Flowright Plumbing & Heating')
        ->assertJsonPath('data.awards.0.name', 'Uwe Brandt');
});

it('hires the candidate with the better record when two apply for the same job', function () {
    $veteranEmployment = employAtSeededPosition('flowright-plumbing', 'office-assistant-3');
    PerformanceReview::create([
        'employment_id' => $veteranEmployment->id,
        'position_id' => $veteranEmployment->position_id,
        'period' => '1997-12',
        'metric_totals' => [],
        'metric_changes' => [],
        'score' => 4,
        'rating' => ReviewRating::Excellent,
        'bonus' => 0,
    ]);
    $veteranEmployment->update(['ended_at' => now()]);

    $opening = JobOpening::query()->whereRelation('company', 'slug', 'rohr-und-sohn')->sole();
    Position::query()->whereBelongsTo($opening)->vacant()->skip(1)->take(5)->get()->each(fn (Position $position) => Employment::factory()->at($position)->create());

    $newcomer = User::factory()->create(['locale' => 'de']);
    $veteran = User::findOrFail($veteranEmployment->user_id);
    JobApplication::factory()->for($newcomer)->for($opening)->create(['responds_at' => now()->subMinute()]);
    JobApplication::factory()->for($veteran)->for($opening)->create(['responds_at' => now()->subMinute()]);

    $this->artisan('careers:review-applications')->assertSuccessful();

    expect(Employment::query()->where('user_id', $veteran->id)->active()->exists())->toBeTrue()
        ->and(Employment::query()->where('user_id', $newcomer->id)->exists())->toBeFalse()
        ->and(JobApplication::query()->where('user_id', $newcomer->id)->sole()->status->value)->toBe('rejected');
});
