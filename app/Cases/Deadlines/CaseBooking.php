<?php

namespace App\Cases\Deadlines;

use App\Cases\WorkCaseKind;
use App\Models\Appointment;
use App\Models\Shipment;
use App\Models\WorkCase;

class CaseBooking
{
    public function isBookedByAssignee(WorkCase $workCase): bool
    {
        return $workCase->kind === WorkCaseKind::Shipment
            ? $this->isShipmentPlannedByAssignee($workCase)
            : $this->isAppointmentBookedByAssignee($workCase);
    }

    private function isShipmentPlannedByAssignee(WorkCase $workCase): bool
    {
        return Shipment::query()
            ->whereKey($workCase->shipment_id)
            ->where('planned_by_employment_id', $workCase->employment_id)
            ->exists();
    }

    private function isAppointmentBookedByAssignee(WorkCase $workCase): bool
    {
        return Appointment::query()
            ->where('booked_by_employment_id', $workCase->employment_id)
            ->where('customer_id', $workCase->customer_id)
            ->notFailed()
            ->exists();
    }
}
