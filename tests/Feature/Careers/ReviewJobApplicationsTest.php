<?php

use App\Careers\Events\PlayerHired;
use App\Careers\JobApplicationStatus;
use App\Models\Company;
use App\Models\Email;
use App\Models\Employment;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\Event;

function dueApplicationAt(Company $company): JobApplication
{
    $opening = JobOpening::factory()->withVacancy()->for($company)->create(['title' => 'Junior Dispatch Coordinator', 'daily_salary' => 120]);

    return JobApplication::factory()->due()->for(User::factory()->locale($company->locale))->for($opening)->create();
}

it('hires the player when the response time has come', function () {
    Event::fake([PlayerHired::class]);
    $this->freezeSecond();
    $application = dueApplicationAt(Company::factory()->create());

    $this->artisan('careers:review-applications')->assertSuccessful();

    expect($application->fresh()?->status)->toBe(JobApplicationStatus::Accepted)
        ->and($application->fresh()?->responded_at?->equalTo(now()))->toBeTrue();

    $employment = Employment::sole();
    expect($employment->user_id)->toBe($application->user_id)
        ->and($employment->position->job_opening_id)->toBe($application->job_opening_id)
        ->and($employment->company_id)->toBe($application->jobOpening->company_id)
        ->and($employment->job_opening_id)->toBe($application->job_opening_id)
        ->and($employment->daily_salary)->toBe(120);
    Event::assertDispatched(PlayerHired::class, fn (PlayerHired $event): bool => $event->employment->is($employment));
});

it('sends the job offer by mail in the company language', function () {
    $company = Company::factory()->locale('de')->create([
        'name' => 'Nordwerk Logistik AG',
        'hr_contact_name' => 'Birgit Kowalski',
        'hr_contact_address' => 'birgit.kowalski@nordwerk.wn',
        'hiring_note' => 'Gerd hat ein Banner gebastelt.',
    ]);
    $application = dueApplicationAt($company);

    $this->artisan('careers:review-applications')->assertSuccessful();

    $email = Email::sole();
    expect($email->user_id)->toBe($application->user_id)
        ->and($email->sender_name)->toBe('Birgit Kowalski')
        ->and($email->sender_address)->toBe('birgit.kowalski@nordwerk.wn')
        ->and($email->subject)->toBe('Ihre Bewerbung als Junior Dispatch Coordinator')
        ->and($email->body)->toContain('willkommen bei Nordwerk Logistik AG')
        ->and($email->body)->toContain('120 Credits pro Tag')
        ->and($email->body)->toContain('Gerd hat ein Banner gebastelt.')
        ->and($email->body)->toContain('Ihr Firmenausweis liegt auf Ihrem Schreibtisch')
        ->and($email->body)->not->toContain('folgen in Kürze')
        ->and($email->read_at)->toBeNull();
});

it('takes over the position of an NPC and leaves the others to their holders', function () {
    $application = dueApplicationAt(Company::factory()->create());
    $taken = Position::query()->whereBelongsTo($application->jobOpening)->sole();
    Position::factory()->for($taken->branch)->create(['job_opening_id' => $application->job_opening_id]);

    $this->artisan('careers:review-applications')->assertSuccessful();

    expect(Employment::sole()->position_id)->toBe($taken->id)
        ->and(Position::query()->vacant()->count())->toBe(1);
});

it('rejects the application when someone else took the last position first', function () {
    $application = dueApplicationAt(Company::factory()->create(['name' => 'Flowright Plumbing']));
    Employment::factory()->at(Position::query()->whereBelongsTo($application->jobOpening)->sole())->create();

    $this->artisan('careers:review-applications')->assertSuccessful();

    expect($application->fresh()?->status)->toBe(JobApplicationStatus::Rejected)
        ->and(Employment::query()->where('user_id', $application->user_id)->exists())->toBeFalse();
    expect(Email::query()->where('user_id', $application->user_id)->sole()->body)
        ->toContain('the position has been filled in the meantime');
});

it('leaves applications alone until their response time', function () {
    $application = JobApplication::factory()->create(['responds_at' => now()->addMinute()]);

    $this->artisan('careers:review-applications')->assertSuccessful();

    expect($application->fresh()?->status)->toBe(JobApplicationStatus::Pending)
        ->and(Employment::count())->toBe(0)
        ->and(Email::count())->toBe(0);
});

it('answers every application only once', function () {
    dueApplicationAt(Company::factory()->create());

    $this->artisan('careers:review-applications')->assertSuccessful();
    $this->artisan('careers:review-applications')->assertSuccessful();

    expect(Employment::count())->toBe(1)
        ->and(Email::count())->toBe(1);
});

it('shows the employer on the player profile', function () {
    $application = dueApplicationAt(Company::factory()->create(['name' => 'TransGlobal Logistics']));

    $this->artisan('careers:review-applications')->assertSuccessful();

    $this->actingAs($application->user)
        ->getJson(route('api.v1.player.show'))
        ->assertJsonPath('data.employer', 'TransGlobal Logistics');
});
