<?php

use App\Cases\WorkCaseStatus;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\City;
use App\Models\Customer;
use App\Models\Person;
use App\Models\WorkCase;
use App\Models\WorldEvent;
use App\Workplace\AppointmentExecutor;
use App\World\Events\EventCatalog;
use App\World\Events\WorldEventHandler;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CompanySeeder;
use Illuminate\Database\Eloquent\Builder;

beforeEach(function () {
    $this->seed([CompanySeeder::class, CitySeeder::class, BranchSeeder::class]);
    $this->city = City::query()->where('slug', 'millbrook')->sole();
    $this->plumber = Branch::query()->where('slug', 'maple-falls')->sole();
});

/**
 * @return Builder<WorldEvent>
 */
function plumbingEvents(): Builder
{
    $types = collect(app(EventCatalog::class)->all())->where('service', 'plumbing')->pluck('type')->all();

    return WorldEvent::query()->whereIn('type', $types);
}

function dripOnMonday(Person $person): WorldEvent
{
    test()->travelTo('2026-09-21 09:00:00');
    $event = WorldEvent::factory()->for($person)->create(['type' => 'dripping_pipe', 'key' => 'test|dripping_pipe']);
    app(WorldEventHandler::class)->handle($event);

    return $event;
}

function carryOutRepairOn(WorldEvent $event, string $date): void
{
    $customer = Customer::query()->whereBelongsTo($event->person)->sole();
    Appointment::factory()->for($customer->branch)->create(['customer_id' => $customer->id, 'date' => $date, 'slot' => '08:00']);

    test()->travelTo("{$date} 11:00:00");
    app(AppointmentExecutor::class)->executeDue();
}

it('records the world events planned for the game day once their time has come', function () {
    $this->travelTo('2026-09-21 07:59:00');
    $this->artisan('game:tick')->assertSuccessful();
    expect(WorldEvent::count())->toBe(0);

    $this->travelTo('2026-09-21 16:00:00');
    $this->artisan('game:tick')->assertSuccessful();

    expect(WorldEvent::query()->whereBelongsTo($this->city)->count())->toBeGreaterThan(0);
});

it('lets the plumber of the city handle household problems as cases for the affected person', function () {
    $this->travelTo('2026-09-21 16:00:00');
    $this->artisan('game:tick')->assertSuccessful();

    $events = plumbingEvents()->whereBelongsTo($this->city)->with(['workCase.customer', 'person'])->get();

    expect($events)->not->toBeEmpty();

    foreach ($events as $event) {
        expect($event->workCase?->branch_id)->toBe($this->plumber->id)
            ->and($event->workCase?->customer->person_id)->toBe($event->person_id)
            ->and($event->person->email_address)->not->toBeNull();
    }

    expect($events->pluck('person_id')->unique())->toHaveCount($events->count());
});

it('records every planned world event only once', function () {
    $this->travelTo('2026-09-21 16:00:00');
    $this->artisan('game:tick')->assertSuccessful();
    $counts = [WorldEvent::count(), WorkCase::count()];

    $this->artisan('game:tick')->assertSuccessful();

    expect([WorldEvent::count(), WorkCase::count()])->toBe($counts);
});

it('plans the same world events for the same city and day', function () {
    $this->travelTo('2026-09-21 16:00:00');
    $this->artisan('game:tick')->assertSuccessful();
    $keys = WorldEvent::query()->orderBy('key')->pluck('key')->all();
    WorkCase::query()->delete();
    WorldEvent::query()->delete();

    $this->artisan('game:tick')->assertSuccessful();

    expect(WorldEvent::query()->orderBy('key')->pluck('key')->all())->toBe($keys);
});

it('records no world events on the weekend', function () {
    $this->travelTo('2026-09-26 16:00:00');

    $this->artisan('game:tick')->assertSuccessful();

    expect(WorldEvent::count())->toBe(0);
});

it('takes a new person on as a customer of the plumber', function () {
    $person = Person::factory()->for($this->city)->create(['household_id' => Person::query()->whereBelongsTo($this->city)->firstOrFail()->household_id]);

    $event = dripOnMonday($person);

    $customer = Customer::query()->whereBelongsTo($person)->sole();
    expect($customer->branch_id)->toBe($this->plumber->id)
        ->and($customer->notes)->toBe('')
        ->and($event->workCase?->customer_id)->toBe($customer->id);
});

it('lets a dripping pipe burst when the repair comes too late', function () {
    $hollis = Person::query()->where('slug', 'margaret-hollis')->sole();
    $drip = dripOnMonday($hollis);

    carryOutRepairOn($drip, '2026-09-24');

    $burst = WorldEvent::query()->where('type', 'burst_pipe')->sole();
    expect($drip->workCase?->fresh()?->status)->toBe(WorkCaseStatus::Resolved)
        ->and($burst->parent_id)->toBe($drip->id)
        ->and($burst->person_id)->toBe($hollis->id)
        ->and($burst->workCase?->case_slug)->toBe('burst-pipe')
        ->and($burst->workCase?->status)->toBe(WorkCaseStatus::Open);
});

it('keeps the pipe intact when the repair is in time', function () {
    $drip = dripOnMonday(Person::query()->where('slug', 'margaret-hollis')->sole());

    carryOutRepairOn($drip, '2026-09-23');

    expect(WorldEvent::query()->where('type', 'burst_pipe')->exists())->toBeFalse();
});

it('records water damage without a case when a burst pipe is repaired too late', function () {
    $this->travelTo('2026-09-21 09:00:00');
    $burst = WorldEvent::factory()->for(Person::query()->where('slug', 'margaret-hollis')->sole())->create(['type' => 'burst_pipe', 'key' => 'test|burst_pipe']);
    app(WorldEventHandler::class)->handle($burst);

    carryOutRepairOn($burst, '2026-09-23');

    $damage = WorldEvent::query()->where('type', 'water_damage')->sole();
    expect($damage->parent_id)->toBe($burst->id)
        ->and($damage->workCase)->toBeNull();
});

it('waits for a reachable person when everyone with an e-mail address already has an open case', function () {
    $this->travelTo('2026-09-21 16:00:00');
    Person::query()->whereBelongsTo($this->city)->whereNotNull('email_address')->each(function (Person $person): void {
        $this->artisan('cases:open', ['company' => 'flowright-plumbing', 'branch' => 'maple-falls', 'template' => 'leaking-pipe', '--customer' => $person->slug]);
    });

    $this->artisan('game:tick')->assertSuccessful();

    expect(plumbingEvents()->whereBelongsTo($this->city)->count())->toBe(0);
});
