<?php

use App\Models\Appointment;
use App\Models\Company;
use App\Models\Technician;
use App\Models\User;

beforeEach(fn () => $this->travelTo('2026-09-18 09:00:00'));

it('shows the next five working days with technicians and their busy slots', function () {
    $company = Company::factory()->create();
    Technician::factory()->for($company)->create([
        'name' => 'Rita Vance',
        'skills' => ['plumbing', 'heating'],
        'busy_slots' => [['weekday' => 1, 'slot' => '08:00']],
    ]);
    $player = playerOnDutyAt($company);

    $this->actingAs($player)
        ->getJson(route('api.v1.schedule.show'))
        ->assertOk()
        ->assertJsonPath('data.days', ['2026-09-21', '2026-09-22', '2026-09-23', '2026-09-24', '2026-09-25'])
        ->assertJsonPath('data.slots', ['08:00', '10:00', '13:00', '15:00'])
        ->assertJsonPath('data.technicians.0.name', 'Rita Vance')
        ->assertJsonPath('data.technicians.0.skills', ['plumbing', 'heating'])
        ->assertJsonPath('data.technicians.0.busy', ['2026-09-21 08:00']);
});

it('shows the appointments of the player', function () {
    $company = Company::factory()->create();
    $player = playerOnDutyAt($company);
    Appointment::factory()->create([
        'employment_id' => $player->employment?->id,
        'date' => '2026-09-22',
        'slot' => '13:00',
    ]);
    Appointment::factory()->create(['date' => '2026-09-22']);

    $this->actingAs($player)
        ->getJson(route('api.v1.schedule.show'))
        ->assertJsonCount(1, 'data.appointments')
        ->assertJsonPath('data.appointments.0.slot', '13:00');
});

it('requires an employment for company software', function () {
    $this->actingAs(User::factory()->create())
        ->getJson(route('api.v1.schedule.show'))
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'not_employed');
});

it('names the company software of the employer', function () {
    $company = Company::factory()->create(['slug' => 'flowright-plumbing', 'office_address' => 'office@flowright.wn']);

    $this->actingAs(playerOnDutyAt($company))
        ->getJson(route('api.v1.company-software.show'))
        ->assertOk()
        ->assertJsonPath('data.office_address', 'office@flowright.wn')
        ->assertJsonPath('data.app_names', ['records' => 'CustomerBase', 'scheduler' => 'ServicePlan']);
});
