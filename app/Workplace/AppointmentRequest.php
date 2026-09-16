<?php

namespace App\Workplace;

use App\Models\Customer;
use App\Models\Technician;
use Carbon\CarbonImmutable;

readonly class AppointmentRequest
{
    public function __construct(
        public Customer $customer,
        public Technician $technician,
        public CarbonImmutable $date,
        public string $slot,
    ) {}
}
