import { useEffect, useState } from 'react';
import { pageLoadProgress } from './page-load';

const TICK_MS = 80;

export function usePageLoad(visitKey: number, durationMs: number): number {
    const [elapsed, setElapsed] = useState({ visitKey, ms: 0 });

    useEffect(() => {
        const startedAt = performance.now();
        const timer = setInterval(() => {
            const ms = performance.now() - startedAt;
            setElapsed({ visitKey, ms });

            if (ms >= durationMs) {
                clearInterval(timer);
            }
        }, TICK_MS);

        return () => clearInterval(timer);
    }, [visitKey, durationMs]);

    const ms = elapsed.visitKey === visitKey ? elapsed.ms : 0;

    return pageLoadProgress(ms, durationMs);
}
