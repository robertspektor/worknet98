import type { Dispatch, SetStateAction } from 'react';
import { useEffect, useRef, useState } from 'react';

type PollOptions<T> = {
    isEnabled?: boolean;
    restartKey?: unknown;
    onArrival?: (next: T, previous: T) => void;
};

export type PolledResource<T> = {
    value: T | null;
    setValue: Dispatch<SetStateAction<T | null>>;
    refresh: () => Promise<void>;
};

export function usePolledResource<T>(
    load: () => Promise<T>,
    intervalMs: number,
    { isEnabled = true, restartKey, onArrival }: PollOptions<T> = {},
): PolledResource<T> {
    const [value, setValue] = useState<T | null>(null);
    const fetched = useRef<T | null>(null);
    const latest = useRef({ load, onArrival });

    useEffect(() => {
        latest.current = { load, onArrival };
    });

    const refresh = () =>
        latest.current
            .load()
            .then((next) => {
                if (fetched.current !== null) {
                    latest.current.onArrival?.(next, fetched.current);
                }
                fetched.current = next;
                setValue(next);
            })
            .catch(() => undefined);

    useEffect(() => {
        if (!isEnabled) {
            return;
        }

        fetched.current = null;
        void refresh();
        const timer = setInterval(() => void refresh(), intervalMs);

        return () => clearInterval(timer);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [isEnabled, intervalMs, restartKey]);

    return { value, setValue, refresh };
}
