import type { ReactNode } from 'react';
import { useCallback, useEffect, useState } from 'react';
import { clockIn, clockOut, heartbeat, show } from '@/routes/api/v1/shift';
import type { ShiftStatus } from '@/types';
import { getJson, postJson } from '../api/game-api';
import { createDeskContext } from '../state/create-desk-context';
import { HEARTBEAT_INTERVAL_MS, isActive } from './activity';
import { useLastInput } from './use-last-input';

type ShiftClock = {
    shift: ShiftStatus | null;
    clockIn: () => Promise<ShiftStatus>;
    clockOut: () => Promise<ShiftStatus>;
};

type ShiftResponse = { data: ShiftStatus };

const { Context, useRequired } = createDeskContext<ShiftClock>('ShiftClock');

export const useShiftClock = useRequired;

export function ShiftClockProvider({ children }: { children: ReactNode }) {
    const [shift, setShift] = useState<ShiftStatus | null>(null);
    const lastInput = useLastInput();

    const refresh = useCallback(
        () =>
            getJson<ShiftResponse>(show.url())
                .then(({ data }) => setShift(data))
                .catch(() => undefined),
        [],
    );

    useEffect(() => {
        void refresh();
    }, [refresh]);

    const isOnDuty = shift?.status === 'on_duty';

    useEffect(() => {
        if (!isOnDuty) {
            return;
        }

        const timer = setInterval(() => {
            if (
                document.visibilityState !== 'visible' ||
                !isActive(lastInput.current, Date.now())
            ) {
                return;
            }

            void postJson<ShiftResponse>(heartbeat.url())
                .then(({ data }) => setShift(data))
                .catch(() => refresh());
        }, HEARTBEAT_INTERVAL_MS);

        return () => clearInterval(timer);
    }, [isOnDuty, lastInput, refresh]);

    const send = async (url: string) => {
        const { data } = await postJson<ShiftResponse>(url);
        setShift(data);

        return data;
    };

    return (
        <Context
            value={{
                shift,
                clockIn: () => send(clockIn.url()),
                clockOut: () => send(clockOut.url()),
            }}
        >
            {children}
        </Context>
    );
}
