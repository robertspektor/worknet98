import type { Replacements } from '@/i18n/translate';
import type { Appointment } from '@/types';

type Translate = (key: string, replacements?: Replacements) => string;

export function bookedByText(appointment: Appointment, t: Translate): string {
    if (appointment.booked_by) {
        return t('service_plan.booked_by', { position: appointment.booked_by });
    }

    if (appointment.booked_by_npc) {
        return t('service_plan.booked_by_npc', {
            name: appointment.booked_by_npc,
        });
    }

    return t('service_plan.booked_by_office');
}
