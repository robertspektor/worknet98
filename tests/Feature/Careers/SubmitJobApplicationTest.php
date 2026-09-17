<?php

use App\Careers\Events\JobApplicationSubmitted;
use App\Careers\JobApplicationStatus;
use App\Models\Company;
use App\Models\Employment;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;

beforeEach(function () {
    config(['game.job_application_response_delay_seconds' => 90]);
    $this->freezeSecond();
});

function applyFor(User $player, JobOpening $opening, array $payload = []): TestResponse
{
    return test()->actingAs($player)->postJson(route('api.v1.job-openings.applications.store', $opening), $payload);
}

it('submits an application that the company answers later', function () {
    Event::fake([JobApplicationSubmitted::class]);
    $player = User::factory()->create();
    $opening = JobOpening::factory()->withVacancy()->create();

    applyFor($player, $opening, ['message' => '  I can type 40 words per minute.  '])
        ->assertCreated()
        ->assertJsonPath('data.job_opening_id', $opening->id)
        ->assertJsonPath('data.status', 'pending');

    $application = JobApplication::sole();
    expect($application->user_id)->toBe($player->id)
        ->and($application->message)->toBe('I can type 40 words per minute.')
        ->and($application->status)->toBe(JobApplicationStatus::Pending)
        ->and($application->responds_at->equalTo(now()->addSeconds(90)))->toBeTrue();
    Event::assertDispatched(JobApplicationSubmitted::class, fn (JobApplicationSubmitted $event): bool => $event->application->is($application));
});

it('stores an empty message as no message', function () {
    applyFor(User::factory()->create(), JobOpening::factory()->withVacancy()->create(), ['message' => '   '])->assertCreated();

    expect(JobApplication::sole()->message)->toBeNull();
});

it('refuses applications to companies in another language', function () {
    $player = User::factory()->locale('de')->create();
    $opening = JobOpening::factory()->for(Company::factory()->locale('en'))->create();

    applyFor($player, $opening)
        ->assertUnprocessable()
        ->assertExactJson([
            'message' => 'Diese Firma liest nur Bewerbungen in ihrer eigenen Sprache.',
            'refusal' => 'language_mismatch',
        ]);

    expect(JobApplication::count())->toBe(0);
});

it('refuses applications to closed positions', function () {
    applyFor(User::factory()->create(), JobOpening::factory()->closed()->create())
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'position_closed');
});

it('refuses a second application to the same opening', function () {
    $application = JobApplication::factory()->accepted()->create();

    applyFor($application->user, $application->jobOpening)
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'already_applied');
});

it('refuses a new application while another one is pending', function () {
    $pending = JobApplication::factory()->create();

    applyFor($pending->user, JobOpening::factory()->create())
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'application_pending');
});

it('refuses applications from employed players', function () {
    $employment = Employment::factory()->create();

    applyFor(User::findOrFail($employment->user_id), JobOpening::factory()->create())
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'already_employed');
});

it('refuses applications when every position is held by a player', function () {
    $opening = JobOpening::factory()->withVacancy()->create();
    Employment::factory()->at(Position::query()->whereBelongsTo($opening)->sole())->create();

    applyFor(User::factory()->create(), $opening)
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'no_vacancy');
});

it('limits the length of the application message', function () {
    applyFor(User::factory()->create(), JobOpening::factory()->create(), ['message' => str_repeat('a', 1001)])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('message');
});

it('lists the applications of the player', function () {
    $player = User::factory()->create();
    $application = JobApplication::factory()->for($player)->create();
    JobApplication::factory()->create();

    $this->actingAs($player)
        ->getJson(route('api.v1.job-applications.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $application->id)
        ->assertJsonPath('data.0.status', 'pending');
});

it('requires a signed-in player to apply', function () {
    $this->postJson(route('api.v1.job-openings.applications.store', JobOpening::factory()->create()))
        ->assertUnauthorized();
});
