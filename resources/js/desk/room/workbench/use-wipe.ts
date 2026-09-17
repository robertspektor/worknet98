import type { PointerEvent as ReactPointerEvent } from 'react';
import { useState } from 'react';

const WIPE_DISTANCE_PX = 900;

function isOverSocket(x: number, y: number, cloth: Element): boolean {
    return document
        .elementsFromPoint(x, y)
        .filter((element) => !cloth.contains(element))
        .some((element) => element.closest('[data-drop="socket"]') !== null);
}

export function useWipe(onWipe: (amount: number) => void) {
    const [offset, setOffset] = useState<{ x: number; y: number } | null>(null);

    const onPointerDown = (event: ReactPointerEvent<HTMLElement>) => {
        if (event.button !== 0) {
            return;
        }

        const element = event.currentTarget;
        const start = { x: event.clientX, y: event.clientY };
        let last = start;
        element.setPointerCapture(event.pointerId);

        const move = (moveEvent: PointerEvent) => {
            const current = { x: moveEvent.clientX, y: moveEvent.clientY };
            setOffset({ x: current.x - start.x, y: current.y - start.y });

            if (isOverSocket(current.x, current.y, element)) {
                const distance = Math.hypot(
                    current.x - last.x,
                    current.y - last.y,
                );
                onWipe(distance / WIPE_DISTANCE_PX);
            }

            last = current;
        };

        const release = () => {
            element.removeEventListener('pointermove', move);
            element.removeEventListener('pointerup', release);
            element.removeEventListener('pointercancel', release);
            setOffset(null);
        };

        element.addEventListener('pointermove', move);
        element.addEventListener('pointerup', release);
        element.addEventListener('pointercancel', release);
    };

    return {
        wipeProps: {
            onPointerDown,
            style: offset
                ? { translate: `${offset.x}px ${offset.y}px`, zIndex: 10 }
                : undefined,
        },
    };
}
