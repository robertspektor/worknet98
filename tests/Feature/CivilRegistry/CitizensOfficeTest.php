<?php

use App\Cases\Metric;
use App\Cases\MetricBook;
use App\Cases\WorkCaseKind;
use App\Cases\WorkCaseStatus;
use App\CivilRegistry\ApplicationCaseOpener;
use App\CivilRegistry\ApplicationKind;
use App\CivilRegistry\Templates\ApplicationTemplateCatalog;
use App\Models\Branch;
use App\Models\CivilApplication;
use App\Models\Customer;
use App\Models\Email;
use App\Models\Household;
use App\Models\Person;
use App\Models\User;
use App\Models\WorkCase;
use App\Models\WorldEvent;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CompanySeeder;

beforeEach(function () {
    $this->travelTo('2026-09-21 09:00:00');
    $this->seed([CompanySeeder::class, CitySeeder::class, BranchSeeder::class]);
    $this->office = Branch::query()->where('slug', 'city-hall-greenvale')->sole();
});

function clerkOnDuty(): User
{
    $clerk = User::findOrFail(employAtSeededPosition('millbrook-city-hall', 'clerk-1')->user_id);
    workAs($clerk, 'POST', 'api.v1.shift.clock-in');
    skipOnboardingTask($clerk);

    return $clerk;
}

function fileApplication(Branch $office, ApplicationKind $kind, Person $applicant, array $attributes): CivilApplication
{
    $event = WorldEvent::factory()->for($applicant)->create(['type' => eventTypeOf($kind)]);
    $application = CivilApplication::create([
        'branch_id' => $office->id,
        'kind' => $kind,
        'applicant_id' => $applicant->id,
        'claimed_district' => $applicant->household->district,
        'claimed_street' => $applicant->household->street,
        'moved_on' => '1998-01-21',
        ...$attributes,
    ]);
    $template = app(ApplicationTemplateCatalog::class)->forKind($office->company, $kind) ?? throw new LogicException('No template.');
    app(ApplicationCaseOpener::class)->open($application, $template, $event);

    return $application;
}

function eventTypeOf(ApplicationKind $kind): string
{
    return match ($kind) {
        ApplicationKind::Move => 'moving',
        ApplicationKind::Marriage => 'marriage',
        ApplicationKind::PetRegistration => 'dog_acquired',
        ApplicationKind::NameChange => 'name_change_wanted',
    };
}

function fileMove(Branch $office, string $personSlug, array $attributes = []): CivilApplication
{
    return fileApplication($office, ApplicationKind::Move, resident($personSlug), [
        'new_district' => 'Brookhaven',
        'new_street' => '99 Sunset Drive',
        ...$attributes,
    ]);
}

function decide(User $clerk, CivilApplication $application, string $decision): void
{
    test()->actingAs($clerk)
        ->postJson(route('api.v1.civil-applications.decision.store', $application), ['decision' => $decision])
        ->assertOk();
}

function resident(string $slug): Person
{
    return Person::query()->whereRelation('city', 'slug', 'millbrook')->where('slug', $slug)->sole();
}

it('receives moves and marriages from the city as applications at the citizens office', function () {
    $this->travelTo('2026-09-21 16:00:00');

    $this->artisan('game:tick')->assertSuccessful();

    $cases = WorkCase::query()->where('kind', WorkCaseKind::Application)->with('civilApplication')->get();
    expect($cases)->not->toBeEmpty()
        ->and($cases->pluck('branch_id')->unique()->sort()->values()->all())->toBe(Branch::query()->whereIn('slug', ['city-hall-greenvale', 'rathaus-gruenau'])->orderBy('id')->pluck('id')->all())
        ->and($cases->every(fn (WorkCase $workCase): bool => $workCase->civilApplication !== null))->toBeTrue();
});

it('sends the clerk the application with the details the applicant gave', function () {
    $clerk = clerkOnDuty();

    fileMove($this->office, 'margaret-hollis');

    $request = Email::query()->where('user_id', $clerk->id)->where('subject', 'Change of address: Margaret Hollis')->sole();
    expect($request->subject)->toBe('Change of address: Margaret Hollis')
        ->and($request->sender_address)->toBe('m.hollis@mail.wn')
        ->and($request->body)->toContain('Previous address: 14 Birch Lane, Maple Falls')
        ->and($request->body)->toContain('New address: 99 Sunset Drive, Brookhaven')
        ->and($request->body)->toContain('Moved in: 01/21/1998');

    $this->actingAs($clerk)->getJson(route('api.v1.civil-applications.index'))
        ->assertOk()
        ->assertJsonPath('data.0.applicant', 'Margaret Hollis')
        ->assertJsonPath('data.0.claimed_street', '14 Birch Lane')
        ->assertJsonPath('data.0.is_own', true)
        ->assertJsonPath('data.0.decision', null);
});

it('moves the household when the clerk approves a correct change of address', function () {
    $clerk = clerkOnDuty();
    $application = fileMove($this->office, 'margaret-hollis');

    decide($clerk, $application, 'approved');

    $household = resident('margaret-hollis')->household;
    expect($household->street)->toBe('99 Sunset Drive')
        ->and($household->district)->toBe('Brookhaven')
        ->and($application->workCase?->fresh()?->status)->toBe(WorkCaseStatus::Resolved)
        ->and(app(MetricBook::class)->valueOf($clerk->employment, Metric::CustomerSatisfaction))->toBe(1)
        ->and(Email::query()->where('user_id', $clerk->id)->where('subject', 'Re: Address change Margaret Hollis')->sole()->body)->toContain('The details match');

    $plumber = playerOnDutyAt(Branch::query()->where('slug', 'maple-falls')->sole());
    $this->actingAs($plumber)->getJson(route('api.v1.customers.index'))->assertOk();
    expect(Customer::query()->ofPerson('margaret-hollis')->whereRelation('branch', 'slug', 'maple-falls')->sole()->person->household->street)->toBe('99 Sunset Drive');
});

it('rewards rejecting an application whose previous address does not match the registry', function () {
    $clerk = clerkOnDuty();
    $application = fileMove($this->office, 'margaret-hollis', ['claimed_street' => '17 Birch Lane']);

    decide($clerk, $application, 'rejected');

    expect(resident('margaret-hollis')->household->street)->toBe('14 Birch Lane')
        ->and(app(MetricBook::class)->valueOf($clerk->employment, Metric::Reliability))->toBe(1)
        ->and(Email::query()->where('user_id', $clerk->id)->where('subject', 'Re: Address change Margaret Hollis')->sole()->body)->toContain('Well spotted');
});

it('lowers reliability when the clerk approves wrong details', function () {
    $clerk = clerkOnDuty();
    $application = fileMove($this->office, 'margaret-hollis', ['claimed_street' => '17 Birch Lane']);

    decide($clerk, $application, 'approved');

    expect(app(MetricBook::class)->valueOf($clerk->employment, Metric::Reliability))->toBe(-2)
        ->and(Email::query()->where('user_id', $clerk->id)->where('subject', 'Re: Address change Margaret Hollis')->sole()->body)->toContain('Now the registry is wrong');
});

it('lowers customer satisfaction when the clerk rejects correct details', function () {
    $clerk = clerkOnDuty();
    $application = fileMove($this->office, 'margaret-hollis');

    decide($clerk, $application, 'rejected');

    expect(app(MetricBook::class)->valueOf($clerk->employment, Metric::CustomerSatisfaction))->toBe(-2)
        ->and(resident('margaret-hollis')->household->street)->toBe('14 Birch Lane');
});

it('lets the spouse move into the applicant household when a marriage is approved', function () {
    $clerk = clerkOnDuty();
    $walter = resident('walter-beck');
    $formerHousehold = $walter->household;
    $application = fileApplication($this->office, ApplicationKind::Marriage, resident('margaret-hollis'), [
        'partner_id' => $walter->id,
        'claimed_partner_district' => $formerHousehold->district,
        'claimed_partner_street' => $formerHousehold->street,
    ]);

    decide($clerk, $application, 'approved');

    expect($walter->fresh()?->household->street)->toBe('14 Birch Lane')
        ->and(Household::query()->whereKey($formerHousehold->id)->exists())->toBeFalse()
        ->and(Email::query()->where('user_id', $clerk->id)->where('subject', 'Marriage: Margaret Hollis and Walter Beck')->exists())->toBeTrue();
});

it('registers a dog without touching the registry and checks the stated address', function () {
    $clerk = clerkOnDuty();
    $application = fileApplication($this->office, ApplicationKind::PetRegistration, resident('margaret-hollis'), ['detail' => 'Toby, beagle']);

    decide($clerk, $application, 'approved');

    expect(resident('margaret-hollis')->name)->toBe('Margaret Hollis')
        ->and(resident('margaret-hollis')->household->street)->toBe('14 Birch Lane')
        ->and(app(MetricBook::class)->valueOf($clerk->employment, Metric::CustomerSatisfaction))->toBe(1)
        ->and(Email::query()->where('user_id', $clerk->id)->where('subject', 'Dog licence: Margaret Hollis')->sole()->body)->toContain('Dog: Toby, beagle');
});

it('renames the resident when the clerk approves a correct change of name', function () {
    $clerk = clerkOnDuty();
    $application = fileApplication($this->office, ApplicationKind::NameChange, resident('margaret-hollis'), ['detail' => 'Margaret Doyle']);

    decide($clerk, $application, 'approved');

    expect(resident('margaret-hollis')->name)->toBe('Margaret Doyle');
});

it('keeps the name when the clerk rejects a change of name with a wrong address', function () {
    $clerk = clerkOnDuty();
    $application = fileApplication($this->office, ApplicationKind::NameChange, resident('margaret-hollis'), [
        'detail' => 'Margaret Doyle',
        'claimed_street' => '17 Birch Lane',
    ]);

    decide($clerk, $application, 'rejected');

    expect(resident('margaret-hollis')->name)->toBe('Margaret Hollis')
        ->and(app(MetricBook::class)->valueOf($clerk->employment, Metric::Reliability))->toBe(1);
});

it('decides every application only once and only by its clerk', function () {
    $clerk = clerkOnDuty();
    $colleague = User::findOrFail(employAtSeededPosition('millbrook-city-hall', 'clerk-2')->user_id);
    $application = fileMove($this->office, 'margaret-hollis');

    $this->actingAs($colleague)
        ->postJson(route('api.v1.civil-applications.decision.store', $application), ['decision' => 'approved'])
        ->assertForbidden();

    decide($clerk, $application, 'approved');

    $this->actingAs($clerk)
        ->postJson(route('api.v1.civil-applications.decision.store', $application), ['decision' => 'rejected'])
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'already_decided');
});

it('lets NPC clerks decide applications correctly', function () {
    $correct = fileMove($this->office, 'margaret-hollis');
    $wrong = fileMove($this->office, 'walter-beck', ['claimed_street' => '1 Nowhere Road']);

    $this->travelTo('2026-09-21 09:30:00');
    $this->artisan('game:tick')->assertSuccessful();

    expect($correct->fresh()?->decision?->value)->toBe('approved')
        ->and($wrong->fresh()?->decision?->value)->toBe('rejected')
        ->and(resident('margaret-hollis')->household->street)->toBe('99 Sunset Drive');
});

it('searches the registry of the own city by name or street', function () {
    $clerk = clerkOnDuty();

    $this->actingAs($clerk)->getJson(route('api.v1.registry.people.index', ['search' => 'birch lane']))
        ->assertOk()
        ->assertJsonFragment(['name' => 'Margaret Hollis', 'street' => '14 Birch Lane', 'district' => 'Maple Falls']);

    $this->actingAs($clerk)->getJson(route('api.v1.registry.people.index', ['search' => 'Hollmann']))
        ->assertOk()
        ->assertJsonCount(0, 'data');

    $this->actingAs($clerk)->getJson(route('api.v1.registry.people.index', ['search' => 'a']))
        ->assertUnprocessable();
});

it('has an application template for every kind at every citizens office', function () {
    foreach (Branch::query()->whereIn('slug', ['city-hall-greenvale', 'rathaus-gruenau'])->with('company')->get() as $office) {
        foreach (ApplicationKind::cases() as $kind) {
            expect(app(ApplicationTemplateCatalog::class)->forKind($office->company, $kind))->not->toBeNull();
        }
    }
});
