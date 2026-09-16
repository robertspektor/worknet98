<?php

namespace App\Cases;

enum MessageTrigger: string
{
    case CaseOpened = 'case_opened';
    case AppointmentBooked = 'appointment_booked';
}
