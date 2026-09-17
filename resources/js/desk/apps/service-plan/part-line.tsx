import { useTranslation } from '@/i18n/use-translation';
import type { Appointment } from '@/types';
import { formatDay } from '../../ui/format';

export function PartLine({ appointment }: { appointment: Appointment }) {
    const { t, locale } = useTranslation();
    const { part } = appointment;

    if (appointment.failed) {
        return (
            <p className="part-line is-failed">{t('service_plan.failed')}</p>
        );
    }

    if (!part) {
        return null;
    }

    const [arrivalDay, arrivalTime] = part.arrival?.split(' ') ?? [];

    return (
        <p className={`part-line is-${part.status}`}>
            {t(`service_plan.part.${part.status}`, {
                contents: part.contents,
                arrival: arrivalDay
                    ? `${formatDay(arrivalDay, locale)} ${arrivalTime}`
                    : '',
            })}
        </p>
    );
}
