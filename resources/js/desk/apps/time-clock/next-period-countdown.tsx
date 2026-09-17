import { useEffect, useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import type { Countdown } from '../../shift/work-time';
import { countdownUntil } from '../../shift/work-time';

const TICK_MS = 30_000;

function countdownKey({ days, hours }: Countdown): string {
    if (days > 0) {
        return 'time_clock.countdown.days_hours';
    }

    return hours > 0
        ? 'time_clock.countdown.hours_minutes'
        : 'time_clock.countdown.minutes';
}

export function NextPeriodCountdown({ endsAt }: { endsAt: string }) {
    const { t } = useTranslation();
    const [nowMs, setNowMs] = useState(() => Date.now());

    useEffect(() => {
        const timer = setInterval(() => setNowMs(Date.now()), TICK_MS);

        return () => clearInterval(timer);
    }, []);

    const countdown = countdownUntil(Date.parse(endsAt), nowMs);

    return (
        <p className="time-clock-note muted">
            {t('time_clock.next_period', {
                countdown: t(countdownKey(countdown), countdown),
            })}
        </p>
    );
}
