<?php

use App\Models\Branch;
use App\Models\Company;
use App\Models\Employment;
use App\Models\JobOpening;
use App\Models\Position;
use App\Models\Shift;
use App\Models\Technician;
use App\Models\User;
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
    return Technician::query()->where('slug', $slug)->sole();
}
