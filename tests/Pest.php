<?php

use App\Models\Company;
use App\Models\Employment;
use App\Models\JobOpening;
use App\Models\Shift;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

function playerOnDutyAt(Company $company): User
{
    $opening = JobOpening::factory()->for($company)->create();
    $employment = Employment::factory()->create(['job_opening_id' => $opening->id, 'company_id' => $company->id]);
    Shift::factory()->create(['employment_id' => $employment->id]);

    return User::findOrFail($employment->user_id);
}

function workAs(User $player, string $method, string $route, array $data = []): void
{
    test()->actingAs($player)->json($method, route($route), $data)->assertSuccessful();
}

function technician(string $slug): Technician
{
    return Technician::query()->where('slug', $slug)->sole();
}
