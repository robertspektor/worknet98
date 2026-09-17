<?php

namespace App\Listeners;

use App\Logistics\PartOrderer;
use App\Workplace\Events\AppointmentBooked;

class OrderSparePartOnAppointmentBooked
{
    public function __construct(private readonly PartOrderer $orderer) {}

    public function handle(AppointmentBooked $event): void
    {
        $this->orderer->orderFor($event->appointment);
    }
}
