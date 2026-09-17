<?php

use App\Cases\Metric;
use App\Cases\MetricBook;
use App\Cases\WorkCaseKind;
use App\Cases\WorkCaseStatus;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Email;
use App\Models\Shipment;
use App\Models\User;
use App\Models\WorkCase;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CompanySeeder;

beforeEach(function () {
    $this->travelTo('2026-09-21 09:00:00');
    $this->seed([CompanySeeder::class, CitySeeder::class, BranchSeeder::class]);
});

function playersOnBothSides(): array
{
    $plumber = User::findOrFail(employAtSeededPosition('flowright-plumbing', 'emergency-dispatcher')->user_id);
    $dispatcher = User::findOrFail(employAtSeededPosition('transglobal-logistics', 'dispatch-coordinator-1')->user_id);
    workAs($plumber, 'POST', 'api.v1.shift.clock-in');
    workAs($dispatcher, 'POST', 'api.v1.shift.clock-in');
    skipOnboardingTask($plumber);
    skipOnboardingTask($dispatcher);

    return [$plumber, $dispatcher];
}

function reportBurstPipeAndBookRepair(User $plumber, string $date, string $slot): Customer
{
    test()->artisan('cases:open', ['company' => 'flowright-plumbing', 'branch' => 'maple-falls', 'template' => 'burst-pipe', '--customer' => 'gloria-mendez'])->assertSuccessful();
    $gloria = Customer::query()->ofPerson('gloria-mendez')->sole();
    workAs($plumber, 'POST', 'api.v1.appointments.store', ['customer_id' => $gloria->id, 'technician_id' => technician('stan-kowalski')->id, 'date' => $date, 'slot' => $slot]);

    return $gloria;
}

it('orders the spare part from the wholesaler and asks the carrier to deliver it when a repair is booked', function () {
    [$plumber, $dispatcher] = playersOnBothSides();

    reportBurstPipeAndBookRepair($plumber, '2026-09-22', '13:00');

    $shipment = Shipment::query()->whereNull('order_key')->with(['branch', 'sender', 'recipient'])->sole();
    $dispatchCase = WorkCase::query()->where('shipment_id', $shipment->id)->sole();
    $request = Email::query()->where('user_id', $dispatcher->id)->where('subject', 'Pickup: shut-off valve 3/4" for Flowright Plumbing & Heating')->sole();

    expect($shipment->branch->slug)->toBe('port-hadley')
        ->and($shipment->sender->slug)->toBe('port-hadley-yard')
        ->and($shipment->recipient->slug)->toBe('maple-falls')
        ->and($shipment->dueAt()->toDateTimeString())->toBe('2026-09-22 13:00:00')
        ->and($dispatchCase->shipment_id)->toBe($shipment->id)
        ->and($dispatchCase->employment?->user_id)->toBe($dispatcher->id)
        ->and($request->sender_address)->toBe('rhonda.mayfield@keystonesupply.wn')
        ->and($request->body)->toContain('Needed by: Tuesday, September 22, 13:00');
});

it('orders nothing for repairs that need no spare part', function () {
    [$plumber] = playersOnBothSides();
    $this->artisan('cases:open', ['company' => 'flowright-plumbing', 'branch' => 'maple-falls', 'template' => 'leaking-pipe', '--customer' => 'frank-deluca'])->assertSuccessful();
    $frank = Customer::query()->ofPerson('frank-deluca')->sole();

    $this->actingAs($plumber)->postJson(route('api.v1.appointments.store'), ['customer_id' => $frank->id, 'technician_id' => technician('stan-kowalski')->id, 'date' => '2026-09-22', 'slot' => '13:00']);

    expect(Shipment::query()->whereNull('order_key')->count())->toBe(0);
});

it('lets the repair go ahead when the dispatcher delivers the part in time', function () {
    [$plumber, $dispatcher] = playersOnBothSides();
    $gloria = reportBurstPipeAndBookRepair($plumber, '2026-09-22', '13:00');
    planShipment($dispatcher, 'rusty-calhoun', '2026-09-22', 'morning');
    workAs($dispatcher, 'POST', 'api.v1.emails.store', ['customer_id' => WorkCase::query()->whereRelation('shipment', 'order_key', null)->sole()->customer_id, 'subject' => 'Route', 'body' => 'Tuesday morning.', 'action' => 'confirm_shipment']);

    deliverShipmentsAt('2026-09-22 10:30:00');
    carryOutAppointmentsAt('2026-09-22 15:00:00');

    $dispatchEmployment = $dispatcher->employment;
    expect(WorkCase::query()->whereRelation('shipment', 'order_key', null)->where('kind', WorkCaseKind::Shipment)->sole()->status)->toBe(WorkCaseStatus::Resolved)
        ->and(WorkCase::query()->where('customer_id', $gloria->id)->sole()->status)->toBe(WorkCaseStatus::Resolved)
        ->and(Appointment::query()->sole()->failed_at)->toBeNull()
        ->and(app(MetricBook::class)->valueOf($dispatchEmployment, Metric::Punctuality))->toBe(1)
        ->and(Email::query()->where('user_id', $dispatcher->id)->where('subject', 'Re: shut-off valve 3/4" for Flowright Plumbing & Heating')->sole()->body)
        ->toContain('At Flowright Plumbing & Heating in time')
        ->toContain('And the right vehicle');
});

it('lets the repair fail on both sides when the part arrives too late', function () {
    [$plumber, $dispatcher] = playersOnBothSides();
    $gloria = reportBurstPipeAndBookRepair($plumber, '2026-09-22', '13:00');
    planShipment($dispatcher, 'rusty-calhoun', '2026-09-22', 'afternoon');

    carryOutAppointmentsAt('2026-09-22 15:00:00');

    $repairCase = WorkCase::query()->where('customer_id', $gloria->id)->sole();
    expect(Appointment::query()->sole()->failed_at)->not->toBeNull()
        ->and($repairCase->status)->toBe(WorkCaseStatus::Open)
        ->and(Email::query()->where('user_id', $plumber->id)->where('subject', 'Gloria Mendez: valve missing')->sole()->sender_name)->toBe('Gary Flowright')
        ->and(Email::query()->where('user_id', $dispatcher->id)->where('subject', 'Complaint: shut-off valve 3/4"')->sole()->sender_name)->toBe('Flowright Plumbing & Heating')
        ->and(app(MetricBook::class)->valueOf($dispatcher->employment, Metric::CustomerSatisfaction))->toBe(-1);

    deliverShipmentsAt('2026-09-22 16:30:00');
    workAs($plumber, 'POST', 'api.v1.appointments.store', ['customer_id' => $gloria->id, 'technician_id' => technician('stan-kowalski')->id, 'date' => '2026-09-23', 'slot' => '13:00']);
    carryOutAppointmentsAt('2026-09-23 15:00:00');

    expect($repairCase->fresh()?->status)->toBe(WorkCaseStatus::Resolved)
        ->and(app(MetricBook::class)->valueOf($dispatcher->employment, Metric::Punctuality))->toBe(-2);
});

it('refuses to load a pallet onto a van', function () {
    [$plumber, $dispatcher] = playersOnBothSides();
    $this->artisan('cases:open', ['company' => 'flowright-plumbing', 'branch' => 'maple-falls', 'template' => 'heating-breakdown', '--customer' => 'gloria-mendez'])->assertSuccessful();
    workAs($plumber, 'POST', 'api.v1.appointments.store', ['customer_id' => Customer::query()->ofPerson('gloria-mendez')->sole()->id, 'technician_id' => technician('rita-vance')->id, 'date' => '2026-09-23', 'slot' => '13:00']);

    $this->actingAs($dispatcher)
        ->putJson(route('api.v1.shipments.plan.update', Shipment::query()->whereNull('order_key')->sole()), ['driver_id' => driver('rusty-calhoun')->id, 'date' => '2026-09-22', 'tour' => 'morning'])
        ->assertUnprocessable();

    expect(Shipment::query()->whereNull('order_key')->sole()->isPlanned())->toBeFalse();
});

it('shows the dispatcher the shipments and the route plan of the own branch', function () {
    [$plumber, $dispatcher] = playersOnBothSides();
    reportBurstPipeAndBookRepair($plumber, '2026-09-22', '13:00');
    planShipment($dispatcher, 'rusty-calhoun', '2026-09-22', 'morning');

    $this->actingAs($dispatcher)->getJson(route('api.v1.shipments.index'))
        ->assertOk()
        ->assertJsonFragment(['contents' => 'shut-off valve 3/4"', 'recipient' => 'Flowright Plumbing & Heating', 'contact' => 'Rhonda Mayfield']);

    $this->actingAs($dispatcher)->getJson(route('api.v1.tour-plan.show'))
        ->assertOk()
        ->assertJsonPath('data.tours.0.id', 'morning')
        ->assertJsonPath('data.drivers.0.name', 'Ed Tanner')
        ->assertJsonPath('data.drivers.0.vehicle', 'truck')
        ->assertJsonPath('data.drivers.0.capacity', 2);

    $this->actingAs($plumber)->getJson(route('api.v1.shipments.index'))->assertOk()->assertJsonCount(0, 'data');
});

it('runs the whole chain with NPCs on both sides', function () {
    $this->artisan('cases:open', ['company' => 'flowright-plumbing', 'branch' => 'maple-falls', 'template' => 'burst-pipe', '--customer' => 'gloria-mendez'])->assertSuccessful();

    foreach (['2026-09-21 09:05:00', '2026-09-21 09:10:00', '2026-09-22 20:00:00', '2026-09-23 20:00:00'] as $time) {
        $this->travelTo($time);
        $this->artisan('game:tick')->assertSuccessful();
    }

    $repairCase = WorkCase::query()
        ->where('customer_id', Customer::query()->ofPerson('gloria-mendez')->whereRelation('branch', 'slug', 'maple-falls')->sole()->id)
        ->where('case_slug', 'burst-pipe')
        ->sole();
    $shipment = Shipment::query()->whereBelongsTo($repairCase, 'repairCase')->sole();
    expect($repairCase->status)->toBe(WorkCaseStatus::Resolved)
        ->and($shipment->delivered_at)->not->toBeNull()
        ->and(Appointment::query()->whereNotNull('failed_at')->count())->toBe(0)
        ->and($shipment->dispatchCase?->status)->toBe(WorkCaseStatus::Resolved);
});
