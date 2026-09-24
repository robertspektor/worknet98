import { useEffect, useState } from 'react';
import { gameNow } from './game-clock';
import { useGameClock } from './use-game-clock';

const TICK_MS = 10_000;

/* The current game time, kept fresh for anything in the world that shows it:
   a clock on the wall, the footer of a terminal. */

export function useGameTime(): Date {
    const settings = useGameClock();
    const [now, setNow] = useState(() => gameNow(settings, Date.now()));

    useEffect(() => {
        const timer = setInterval(
            () => setNow(gameNow(settings, Date.now())),
            TICK_MS,
        );

        return () => clearInterval(timer);
    }, [settings]);

    return now;
}
