<?php

use App\Cases\WorkCaseKind;
use App\Logistics\FreeTourFinder;
use App\Models\Email;
use App\Models\Position;
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

function startWorking(string $company, string $position): User
{
    $employment = employAtSeededPosition($company, $position);
    $player = User::findOrFail($employment->user_id);
    workAs($player, 'POST', 'api.v1.shift.clock-in');

    return $player;
}

it('briefs a new dispatcher and puts a first shipment on their desk', function () {
    $player = startWorking('nordwerk-logistik', 'dispatch-coordinator-2');

    $briefing = Email::query()->where('user_id', $player->id)->where('subject', 'Willkommen in der Disposition')->sole();
    $case = WorkCase::query()->where('employment_id', $player->employment?->id)->sole();

    expect($briefing->body)->toContain('Tourenplaner')
        ->and($case->kind)->toBe(WorkCaseKind::Shipment)
        ->and($case->position->slug)->toBe('dispatch-coordinator-2')
        ->and(Shipment::query()->sole()->order_key)->toBe("onboarding|{$player->employment?->id}");
});

it('briefs a new clerk and puts a first application on their desk', function () {
    $player = startWorking('millbrook-city-hall', 'clerk-3');

    $case = WorkCase::query()->where('employment_id', $player->employment?->id)->sole();

    expect(Email::query()->where('user_id', $player->id)->where('subject', 'Welcome to the citizens office')->exists())->toBeTrue()
        ->and($case->kind)->toBe(WorkCaseKind::Application)
        ->and($case->position->slug)->toBe('clerk-3')
        ->and($case->civilApplication?->applicant->city->slug)->toBe('millbrook');
});

it('leaves the scripted first day of the plumbers alone', function () {
    $player = startWorking('rohr-und-sohn', 'office-assistant-1');

    $cases = WorkCase::query()->where('employment_id', $player->employment?->id)->get();

    expect($cases)->toHaveCount(1)
        ->and($cases->first()?->kind)->toBe(WorkCaseKind::Scripted);
});

it('welcomes a player only on the first shift', function () {
    $player = startWorking('nordwerk-logistik', 'dispatch-coordinator-2');
    workAs($player, 'POST', 'api.v1.shift.clock-out');
    workAs($player, 'POST', 'api.v1.shift.clock-in');

    expect(Email::query()->where('user_id', $player->id)->where('subject', 'Willkommen in der Disposition')->count())->toBe(1)
        ->and(Shipment::count())->toBe(1);
});

it('offers a ladder with a new task and more money at every playable employer', function () {
    $entryPositions = Position::query()->whereNotNull('job_opening_id')->whereRelation('jobOpening', 'is_open', true)->with('branch.company')->get();

    expect($entryPositions)->not->toBeEmpty();

    foreach ($entryPositions as $position) {
        $rung = $position;
        $steps = 0;

        while ($rung->promotionTargets()->exists()) {
            $next = $rung->promotionTargets()->with('promotionTargets')->get();

            foreach ($next as $target) {
                expect($target->responsibilities)->not->toBeEmpty("Position [{$target->slug}] has no work.")
                    ->and($target->daily_salary)->toBeGreaterThan($rung->daily_salary);
            }

            $rung = $next->first();
            $steps++;
        }

        expect($steps)->toBeGreaterThanOrEqual(2, "Company [{$position->branch->company->slug}] has no ladder.");
    }
});

it('gives a new dispatcher a first shipment that a free tour still reaches in time', function () {
    $this->travelTo('2026-09-23 09:00:00');
    startWorking('nordwerk-logistik', 'dispatch-coordinator-2');

    $shipment = Shipment::query()->sole();

    expect(app(FreeTourFinder::class)->bestFor($shipment)?->arrival()->lte($shipment->dueAt()))->toBeTrue();
});
