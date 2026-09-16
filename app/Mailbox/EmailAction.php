<?php

namespace App\Mailbox;

enum EmailAction: string
{
    case ConfirmAppointment = 'confirm_appointment';
    case RequestDetails = 'request_details';
    case Other = 'other';
}
