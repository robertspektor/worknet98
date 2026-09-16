<?php

namespace App\Workplace;

use App\Game\Refusal;

enum BookingRefusal: string implements Refusal
{
    case OutsideBookingWindow = 'outside_booking_window';
    case TechnicianBusy = 'technician_busy';
    case SlotTaken = 'slot_taken';

    public function message(): string
    {
        return __('service_plan.refusal.'.$this->value);
    }
}
