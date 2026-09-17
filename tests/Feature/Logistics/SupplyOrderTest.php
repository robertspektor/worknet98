<?php

use App\Cases\Metric;
use App\Cases\MetricBook;
use App\Cases\WorkCaseKind;
use App\Logistics\ShipmentDispatch;
use App\Logistics\Supply\SupplyRouteCatalog;
use App\Logistics\Templates\ShipmentTemplateCatalog;
use App\Models\Branch;
use App\Models\City;
use App\Models\Company;
use App\Models\Email;
use App\Models\Position;
use App\Models\Shipment;
use App\Models\User;
use App\Models\WorkCase;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CompanySeeder;

beforeEach(function () {
    $this->seed([CompanySeeder::class, CitySeeder::class, BranchSeeder::class]);
    $this->millbrook = City::query()->where('slug', 'millbrook')->sole();
});

function branchOfCompany(string $slug): Branch
{
    return Branch::query()->whereRelation('company', 'slug', $slug)->sole();
}

function sendDrugstoreRestock(string $dueDate): Shipment
{
    $shipment = app(ShipmentDispatch::class)->send(City::query()->where('slug', 'millbrook')->sole(), branchOfCompany('happy-consumer-products'), branchOfCompany('cranes-drugstore'), ShipmentTemplateCatalog::RESTOCK_DELIVERY, [
        'order_key' => 'test|restock',
        'contents' => 'dish brushes, 1 carton',
        'size' => 'parcel',
        'due_date' => $dueDate,
        'due_slot' => '12:00',
    ]);

    return $shipment ?? throw new LogicException('No carrier.');
}

it('places the supply orders planned for the game day once their time has come', function () {
    $this->travelTo('2026-09-21 07:59:00');
    $this->artisan('game:tick')->assertSuccessful();
    expect(Shipment::count())->toBe(0);

    $this->travelTo('2026-09-21 16:00:00');
    $this->artisan('game:tick')->assertSuccessful();

    $shipments = Shipment::query()->whereNotNull('order_key')->with(['branch.company', 'sender.company', 'recipient.company', 'dispatchCase'])->get();

    expect($shipments)->not->toBeEmpty();

    foreach ($shipments as $shipment) {
        expect($shipment->branch->company->offers('freight'))->toBeTrue()
            ->and($shipment->sender->city_id)->toBe($shipment->branch->city_id)
            ->and($shipment->recipient->city_id)->toBe($shipment->branch->city_id)
            ->and($shipment->dueAt()->gt(now()))->toBeTrue()
            ->and($shipment->dispatchCase?->kind)->toBe(WorkCaseKind::Shipment)
            ->and($shipment->dispatchCase?->case_slug)->toBe('restock-delivery');
    }
});

it('places every planned supply order only once', function () {
    $this->travelTo('2026-09-21 16:00:00');
    $this->artisan('game:tick')->assertSuccessful();
    $count = Shipment::count();

    $this->artisan('game:tick')->assertSuccessful();

    expect(Shipment::count())->toBe($count);
});

it('places no supply orders on the weekend', function () {
    $this->travelTo('2026-09-26 16:00:00');

    $this->artisan('game:tick')->assertSuccessful();

    expect(Shipment::count())->toBe(0);
});

it('asks the dispatcher to pick up the restock at the supplier', function () {
    $this->travelTo('2026-09-21 09:00:00');
    $dispatcher = User::findOrFail(employAtSeededPosition('transglobal-logistics', 'dispatch-coordinator-1')->user_id);

    sendDrugstoreRestock('2026-09-22');

    $request = Email::query()->where('user_id', $dispatcher->id)->sole();
    expect($request->subject)->toBe("Pickup: dish brushes, 1 carton for Crane's Drugstore")
        ->and($request->sender_name)->toBe('Judy Brennan')
        ->and($request->sender_address)->toBe('judy.brennan@happyconsumer.wn')
        ->and($request->body)->toContain('Needed by: Tuesday, September 22, 12:00')
        ->and($request->body)->toContain("Deliver to: Crane's Drugstore, Lakeside Park");
});

it('lets the shop complain when the restock arrives late', function () {
    $this->travelTo('2026-09-21 09:00:00');
    $dispatcher = User::findOrFail(employAtSeededPosition('transglobal-logistics', 'dispatch-coordinator-1')->user_id);
    workAs($dispatcher, 'POST', 'api.v1.shift.clock-in');
    $shipment = sendDrugstoreRestock('2026-09-22');

    planShipment($dispatcher, 'rusty-calhoun', '2026-09-22', 'afternoon');
    deliverShipmentsAt('2026-09-22 16:30:00');

    $complaint = Email::query()->where('user_id', $dispatcher->id)->where('subject', 'Complaint: dish brushes, 1 carton')->sole();
    expect($shipment->fresh()?->delivered_at)->not->toBeNull()
        ->and($complaint->sender_name)->toBe("Crane's Drugstore")
        ->and($complaint->body)->toContain('empty shelf')
        ->and(app(MetricBook::class)->valueOf($dispatcher->employment, Metric::CustomerSatisfaction))->toBe(-1)
        ->and(app(MetricBook::class)->valueOf($dispatcher->employment, Metric::Punctuality))->toBe(-2);
});

it('keeps the shop quiet when the restock arrives in time', function () {
    $this->travelTo('2026-09-21 09:00:00');
    $dispatcher = User::findOrFail(employAtSeededPosition('transglobal-logistics', 'dispatch-coordinator-1')->user_id);
    workAs($dispatcher, 'POST', 'api.v1.shift.clock-in');
    sendDrugstoreRestock('2026-09-22');

    planShipment($dispatcher, 'rusty-calhoun', '2026-09-22', 'morning');
    deliverShipmentsAt('2026-09-22 10:30:00');

    expect(Email::query()->where('user_id', $dispatcher->id)->where('subject', 'like', 'Complaint:%')->exists())->toBeFalse()
        ->and(WorkCase::query()->where('kind', WorkCaseKind::Shipment)->sole()->status->value)->toBe('resolved');
});

it('only runs supply routes between companies of the same city with a sales desk at the supplier', function () {
    foreach (City::all() as $city) {
        foreach (app(SupplyRouteCatalog::class)->routesIn($city) as $route) {
            $supplier = Branch::query()->whereBelongsTo($city)->whereRelation('company', 'slug', $route->supplier)->first();
            $recipient = Branch::query()->whereBelongsTo($city)->whereRelation('company', 'slug', $route->recipient)->first();

            expect($supplier)->not->toBeNull("Supplier [{$route->supplier}] is not in [{$city->slug}].")
                ->and($recipient)->not->toBeNull("Recipient [{$route->recipient}] is not in [{$city->slug}].")
                ->and(Position::query()->whereBelongsTo($supplier)->responsibleFor('sales')->exists())->toBeTrue();
        }
    }

    foreach (Company::all()->filter(fn (Company $company): bool => $company->offers('freight')) as $carrier) {
        expect(app(ShipmentTemplateCatalog::class)->find($carrier, ShipmentTemplateCatalog::RESTOCK_DELIVERY))->not->toBeNull();
    }
});
