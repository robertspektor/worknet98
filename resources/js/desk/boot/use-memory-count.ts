import { useEffect, useState } from 'react';
import { countedMemoryKb } from './memory-count';

const TICK_MS = 60;

export function useMemoryCount(
    isCounting: boolean,
    durationMs: number,
): number {
    const [elapsedMs, setElapsedMs] = useState(0);

    useEffect(() => {
        if (!isCounting) {
            return;
        }

        const startedAt = performance.now();
        const timer = setInterval(
            () => setElapsedMs(performance.now() - startedAt),
            TICK_MS,
        );

        return () => clearInterval(timer);
    }, [isCounting]);

    return countedMemoryKb(elapsedMs, durationMs);
}
