import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { useTimedSteps } from '../boot/use-timed-steps';
import { formatGameDate, gameNow } from '../clock/game-clock';
import { useGameClock } from '../clock/use-game-clock';

const HOLD_MS = 2000;
const FADE_MS = 900;

/* Arriving somewhere: where you are and what day it is, on black, before the
   room appears. */

export function ArrivalVeil({
    city,
    isFirstDay,
}: {
    city: string | null;
    isFirstDay: boolean;
}) {
    const { t, locale } = useTranslation();
    const clock = useGameClock();
    const [isGone, setGone] = useState(false);
    const [date] = useState(() =>
        formatGameDate(gameNow(clock, Date.now()), locale),
    );
    const step = useTimedSteps(
        2,
        (current) => (current === 1 ? HOLD_MS : FADE_MS),
        () => setGone(true),
    );

    if (isGone) {
        return null;
    }

    return (
        <div className={`arrival-veil ${step > 1 ? 'is-fading' : ''}`}>
            <p className="arrival-place">
                {city === null
                    ? t('arrival.home')
                    : t('arrival.place', { city })}
            </p>
            <p className="arrival-date">{date}</p>
            {isFirstDay && (
                <p className="arrival-first-day">{t('arrival.first_day')}</p>
            )}
        </div>
    );
}
