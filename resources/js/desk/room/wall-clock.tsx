import type { CSSProperties } from 'react';
import { clockHands } from '../clock/game-clock';
import { useGameTime } from '../clock/use-game-time';

const MARKS = [0, 90, 180, 270];

/* The clock every office of the city has on the wall. It runs on game time,
   so the hall agrees with the date the player arrives home to. */

export function WallClock() {
    const hands = clockHands(useGameTime());

    return (
        <div className="wall-clock" aria-hidden="true">
            <span className="clock-face">
                {MARKS.map((angle) => (
                    <span
                        key={angle}
                        className="clock-mark"
                        style={{ rotate: `${angle}deg` }}
                    />
                ))}
                <span
                    className="clock-hand is-hours"
                    style={{ rotate: `${hands.hours}deg` } as CSSProperties}
                />
                <span
                    className="clock-hand is-minutes"
                    style={{ rotate: `${hands.minutes}deg` } as CSSProperties}
                />
                <span className="clock-pin" />
            </span>
        </div>
    );
}
