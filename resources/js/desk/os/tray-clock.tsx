import { useEffect, useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { formatGameTime, gameNow } from '../clock/game-clock';
import { useGameClock } from '../clock/use-game-clock';

const TICK_MS = 2_000;

export function TrayClock() {
    const { locale } = useTranslation();
    const settings = useGameClock();
    const [realMs, setRealMs] = useState(() => Date.now());

    useEffect(() => {
        const timer = setInterval(() => setRealMs(Date.now()), TICK_MS);

        return () => clearInterval(timer);
    }, []);

    return (
        <span className="tray-clock">
            {formatGameTime(gameNow(settings, realMs), locale)}
        </span>
    );
}
