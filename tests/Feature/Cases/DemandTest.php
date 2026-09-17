<?php

use App\Cases\WorkCaseKind;
use App\Models\Branch;
use App\Models\WorkCase;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CompanySeeder;

beforeEach(function () {
    $this->seed([CompanySeeder::class, BranchSeeder::class]);
    $this->branch = Branch::query()->where('slug', 'maple-falls')->sole();
});

it('opens the cases planned for the game day once their time has come', function () {
    $this->travelTo('2026-09-21 07:59:00');
    $this->artisan('game:tick')->assertSuccessful();
    expect(WorkCase::count())->toBe(0);

    $this->travelTo('2026-09-21 16:00:00');
    $this->artisan('game:tick')->assertSuccessful();

    $cases = WorkCase::query()->whereBelongsTo($this->branch)->get();
    expect($cases)->not->toBeEmpty()
        ->and($cases->every(fn (WorkCase $workCase): bool => $workCase->kind === WorkCaseKind::Template && $workCase->demand_key !== null))->toBeTrue()
        ->and($cases->pluck('customer_id')->unique())->toHaveCount($cases->count());
});

it('opens every planned case only once', function () {
    $this->travelTo('2026-09-21 16:00:00');
    $this->artisan('game:tick')->assertSuccessful();
    $count = WorkCase::count();

    $this->artisan('game:tick')->assertSuccessful();

    expect(WorkCase::count())->toBe($count);
});

it('plans the same demand for the same branch and day', function () {
    $this->travelTo('2026-09-21 16:00:00');
    $this->artisan('game:tick')->assertSuccessful();
    $keys = WorkCase::query()->orderBy('demand_key')->pluck('demand_key')->all();
    WorkCase::query()->delete();

    $this->artisan('game:tick')->assertSuccessful();

    expect(WorkCase::query()->orderBy('demand_key')->pluck('demand_key')->all())->toBe($keys);
});

it('opens no cases on the weekend', function () {
    $this->travelTo('2026-09-26 16:00:00');

    $this->artisan('game:tick')->assertSuccessful();

    expect(WorkCase::count())->toBe(0);
});

it('waits for a free customer when every customer already has an open case', function () {
    $this->travelTo('2026-09-21 16:00:00');
    foreach ($this->branch->customers()->get() as $customer) {
        $this->artisan('cases:open', ['company' => 'flowright-plumbing', 'branch' => 'maple-falls', 'template' => 'leaking-pipe', '--customer' => $customer->slug]);
    }

    $this->artisan('game:tick')->assertSuccessful();

    expect(WorkCase::query()->whereBelongsTo($this->branch)->whereNotNull('demand_key')->count())->toBe(0);
});
