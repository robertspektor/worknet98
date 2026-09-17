<?php

namespace App\Http\Requests;

use App\Game\GameClock;
use App\Logistics\ShipmentPlan;
use App\Logistics\Tour;
use App\Models\Driver;
use App\Models\Shipment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlanShipmentRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'driver_id' => ['required', 'integer', Rule::exists('drivers', 'id')->where('branch_id', $this->branchId())],
            'date' => ['required', 'date_format:Y-m-d'],
            'tour' => ['required', Rule::enum(Tour::class)],
        ];
    }

    public function plan(Shipment $shipment): ShipmentPlan
    {
        return new ShipmentPlan(
            shipment: $shipment,
            driver: Driver::query()->findOrFail($this->integer('driver_id')),
            date: CarbonImmutable::createFromFormat('Y-m-d', $this->string('date')->toString())?->startOfDay() ?? app(GameClock::class)->today(),
            tour: Tour::from($this->string('tour')->toString()),
        );
    }

    private function branchId(): ?int
    {
        $player = $this->user();

        return $player instanceof User ? $player->employment?->position->branch_id : null;
    }
}
