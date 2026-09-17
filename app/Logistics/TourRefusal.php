<?php

namespace App\Logistics;

use App\Game\Refusal;

enum TourRefusal: string implements Refusal
{
    case OutsideBookingWindow = 'outside_booking_window';
    case VehicleTooSmall = 'vehicle_too_small';
    case DriverBusy = 'driver_busy';
    case TourFull = 'tour_full';
    case AlreadyDelivered = 'already_delivered';

    public function message(): string
    {
        return __('tour_planner.refusal.'.$this->value);
    }
}
