import type { PointerEvent as ReactPointerEvent } from 'react';
import { useEffect, useRef, useState } from 'react';

const TICK_MS = 100;
const AMOUNT_PER_TICK = 0.1;
const AMOUNT_PER_TAP = 0.2;

export function useHold(
    onSqueeze: (amount: number) => void,
    onRelease: () => void,
) {
    const [isHolding, setHolding] = useState(false);
    const onSqueezeRef = useRef(onSqueeze);
    const ticksRef = useRef(0);

    useEffect(() => {
        onSqueezeRef.current = onSqueeze;
    }, [onSqueeze]);

    useEffect(() => {
        if (!isHolding) {
            return;
        }

        const timer = setInterval(() => {
            ticksRef.current += 1;
            onSqueezeRef.current(AMOUNT_PER_TICK);
        }, TICK_MS);

        return () => clearInterval(timer);
    }, [isHolding]);

    const onPointerDown = (event: ReactPointerEvent<HTMLElement>) => {
        if (event.button !== 0) {
            return;
        }

        event.currentTarget.setPointerCapture(event.pointerId);
        ticksRef.current = 0;
        setHolding(true);
    };

    const release = () => {
        if (!isHolding) {
            return;
        }

        if (ticksRef.current === 0) {
            onSqueeze(AMOUNT_PER_TAP);
        }

        setHolding(false);
        onRelease();
    };

    return {
        isHolding,
        holdProps: {
            onPointerDown,
            onPointerUp: release,
            onPointerCancel: release,
        },
    };
}
