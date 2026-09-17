<?php

use App\Logistics\TourExecutor;
use App\Models\Branch;
use App\Models\CivilApplication;
use App\Models\Company;
use App\Models\Driver;
use App\Models\Employment;
use App\Models\JobOpening;
use App\Models\LedgerEntry;
use App\Models\Position;
use App\Models\Shift;
use App\Models\Shipment;
use App\Models\Technician;
use App\Models\User;
use App\Models\WorkCase;
use App\Models\WorldEvent;
use App\Work\LedgerReason;
use App\Workplace\AppointmentExecutor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

function playerOnDutyAt(Company|Branch $workplace): User
{
    $branch = $workplace instanceof Branch ? $workplace : Branch::factory()->for($workplace)->create();
    $opening = JobOpening::factory()->for($branch->company)->create();
    $position = Position::factory()->for($branch)->create(['job_opening_id' => $opening->id]);
    $employment = Employment::factory()->at($position)->create();
    Shift::factory()->create(['employment_id' => $employment->id]);

    return User::findOrFail($employment->user_id);
}

function employAtSeededPosition(string $companySlug, string $positionSlug = 'office-assistant-1'): Employment
{
    $position = Position::query()
        ->whereRelation('branch.company', 'slug', $companySlug)
        ->where('slug', $positionSlug)
        ->sole();

    return Employment::factory()->at($position)->create();
}

function workAs(User $player, string $method, string $route, array $data = []): void
{
    test()->actingAs($player)->json($method, route($route), $data)->assertSuccessful();
}

function technician(string $slug): Technician
{
    return Technician::query()->ofPerson($slug)->sole();
}

function carryOutAppointmentsAt(string $time): void
{
    test()->travelTo($time);
    app(AppointmentExecutor::class)->executeDue();
}

function playerWithCredits(int $credits): User
{
    $player = User::factory()->create();
    LedgerEntry::create(['user_id' => $player->id, 'amount' => $credits, 'reason' => LedgerReason::Salary]);

    return $player;
}

function driver(string $slug): Driver
{
    return Driver::query()->whereRelation('person', 'slug', $slug)->sole();
}

function skipOnboardingTask(User $player): void
{
    WorkCase::query()->where('employment_id', $player->employment?->id)->delete();
    Shipment::query()->where('order_key', 'like', 'onboarding|%')->delete();
    WorldEvent::query()->where('key', 'like', 'onboarding|%')->delete();
    CivilApplication::query()->whereDoesntHave('workCase')->delete();
}

function planShipment(User $dispatcher, string $driver, string $date, string $tour, ?Shipment $shipment = null): Shipment
{
    $shipment ??= Shipment::query()->whereNull('order_key')->sole();
    test()->actingAs($dispatcher)
        ->putJson(route('api.v1.shipments.plan.update', $shipment), ['driver_id' => driver($driver)->id, 'date' => $date, 'tour' => $tour])
        ->assertOk();

    return $shipment->fresh() ?? $shipment;
}

function deliverShipmentsAt(string $time): void
{
    test()->travelTo($time);
    app(TourExecutor::class)->executeDue();
}
