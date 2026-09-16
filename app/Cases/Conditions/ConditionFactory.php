<?php

namespace App\Cases\Conditions;

use App\Mailbox\EmailAction;
use InvalidArgumentException;

class ConditionFactory
{
    /**
     * @param  array<string, mixed>  $definition
     */
    public function make(array $definition): Condition
    {
        $customer = (string) $definition['customer'];

        return match ($definition['type']) {
            'appointment_booked' => new AppointmentBooked($customer),
            'appointment_between' => new AppointmentBetween($customer, (string) $definition['from'], (string) $definition['to']),
            'appointment_within_work_days' => new AppointmentWithinWorkDays($customer, (int) $definition['days']),
            'technician_has_skill' => new TechnicianHasSkill($customer, (string) $definition['skill']),
            'email_sent' => new EmailSent($customer, EmailAction::from((string) $definition['action'])),
            'calendar_entry_for_appointment' => new CalendarEntryForAppointment($customer),
            default => throw new InvalidArgumentException("Unknown case condition [{$definition['type']}]."),
        };
    }
}
