<?php

namespace App\Http\Requests;

use App\Game\GameClock;
use App\Models\Customer;
use App\Models\Technician;
use App\Models\User;
use App\Workplace\AppointmentRequest;
use App\Workplace\ServiceSlots;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookAppointmentRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $branchId = $this->branchId();

        return [
            'customer_id' => ['required', 'integer', Rule::exists('customers', 'id')->where('branch_id', $branchId)],
            'technician_id' => ['required', 'integer', Rule::exists('technicians', 'id')->where('branch_id', $branchId)],
            'date' => ['required', 'date_format:Y-m-d'],
            'slot' => ['required', Rule::in(ServiceSlots::ALL)],
        ];
    }

    public function appointment(): AppointmentRequest
    {
        return new AppointmentRequest(
            customer: Customer::query()->findOrFail($this->integer('customer_id')),
            technician: Technician::query()->findOrFail($this->integer('technician_id')),
            date: CarbonImmutable::createFromFormat('Y-m-d', $this->string('date')->toString())?->startOfDay() ?? app(GameClock::class)->today(),
            slot: $this->string('slot')->toString(),
        );
    }

    private function branchId(): ?int
    {
        $player = $this->user();

        return $player instanceof User ? $player->employment?->position->branch_id : null;
    }
}
