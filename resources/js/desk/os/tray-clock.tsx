import { useEffect, useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';

const TICK_MS = 10_000;

export function TrayClock() {
    const { locale } = useTranslation();
    const [now, setNow] = useState(() => new Date());

    useEffect(() => {
        const timer = setInterval(() => setNow(new Date()), TICK_MS);

        return () => clearInterval(timer);
    }, []);

    return (
        <span className="tray-clock">
            {now.toLocaleTimeString(locale, {
                hour: '2-digit',
                minute: '2-digit',
            })}
        </span>
    );
}
