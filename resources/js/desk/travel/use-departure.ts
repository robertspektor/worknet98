import { useEffect, useRef, useState } from 'react';
import type { DeparturePhase } from './departure';
import { DEPARTURE_DELAY_MS, nextDeparturePhase } from './departure';

/* The way out runs on its own once it has started: the line drops, the
   screen goes dark and only then does the page follow. */

export function useDeparture(steps: {
    onDark: () => void;
    onLeave: () => void;
}) {
    const [phase, setPhase] = useState<DeparturePhase>('here');
    const latest = useRef(steps);
    latest.current = steps;

    useEffect(() => {
        if (phase === 'here' || phase === 'leaving') {
            return;
        }

        const timer = setTimeout(() => {
            const next = nextDeparturePhase(phase);

            if (next === 'dark') {
                latest.current.onDark();
            }

            if (next === 'leaving') {
                latest.current.onLeave();
            }

            setPhase(next);
        }, DEPARTURE_DELAY_MS[phase]);

        return () => clearTimeout(timer);
    }, [phase]);

    return { phase, depart: () => setPhase('disconnecting') };
}
