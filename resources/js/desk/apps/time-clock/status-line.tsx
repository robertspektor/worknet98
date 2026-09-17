import { useTranslation } from '@/i18n/use-translation';
import type { ShiftStatus } from '@/types';
import { formatGameTime } from '../../clock/game-clock';

export function StatusLine({ shift }: { shift: ShiftStatus }) {
    const { t, locale } = useTranslation();
    const time = shift.clocked_in_at
        ? formatGameTime(new Date(`${shift.clocked_in_at}Z`), locale)
        : '';

    return (
        <>
            <p className={`time-clock-status is-${shift.status}`}>
                {t(`time_clock.status.${shift.status}`, { time })}
            </p>
            <p className="time-clock-note muted">
                {shift.clocked_out_automatically
                    ? t('time_clock.auto_clocked_out')
                    : t('time_clock.active_only')}
            </p>
        </>
    );
}
