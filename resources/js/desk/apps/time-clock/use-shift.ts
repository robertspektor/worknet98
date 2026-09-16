import { useEffect, useState } from 'react';
import { clockIn, clockOut, show } from '@/routes/api/v1/shift';
import type { ShiftStatus } from '@/types';
import { getJson, postJson } from '../../api/game-api';

type ShiftClock = {
    shift: ShiftStatus | null;
    clockIn: () => Promise<ShiftStatus>;
    clockOut: () => Promise<ShiftStatus>;
};

export function useShift(): ShiftClock {
    const [shift, setShift] = useState<ShiftStatus | null>(null);

    useEffect(() => {
        let isCurrent = true;

        void getJson<{ data: ShiftStatus }>(show.url()).then(({ data }) => {
            if (isCurrent) {
                setShift(data);
            }
        });

        return () => {
            isCurrent = false;
        };
    }, []);

    const send = async (url: string) => {
        const { data } = await postJson<{ data: ShiftStatus }>(url);
        setShift(data);

        return data;
    };

    return {
        shift,
        clockIn: () => send(clockIn.url()),
        clockOut: () => send(clockOut.url()),
    };
}
