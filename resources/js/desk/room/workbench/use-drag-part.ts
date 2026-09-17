import type { PointerEvent as ReactPointerEvent } from 'react';
import { useRef, useState } from 'react';

export type DropTarget = 'case' | 'socket' | 'mat' | 'bench';

type Offset = { x: number; y: number };

const MIN_DRAG_DISTANCE = 12;

function dropTargetAt(
    x: number,
    y: number,
    dragged: Element,
): DropTarget | null {
    const target = document
        .elementsFromPoint(x, y)
        .filter((element) => !dragged.contains(element))
        .map((element) => element.closest<HTMLElement>('[data-drop]'))
        .find((element) => element !== null);

    return (target?.dataset.drop as DropTarget | undefined) ?? null;
}

export function useDragPart(onDrop: (target: DropTarget | null) => void) {
    const [offset, setOffset] = useState<Offset | null>(null);
    const wasDraggedRef = useRef(false);

    const onPointerDown = (event: ReactPointerEvent<HTMLElement>) => {
        if (event.button !== 0) {
            return;
        }

        wasDraggedRef.current = false;
        const element = event.currentTarget;
        const start = { x: event.clientX, y: event.clientY };
        element.setPointerCapture(event.pointerId);

        const move = (moveEvent: PointerEvent) =>
            setOffset({
                x: moveEvent.clientX - start.x,
                y: moveEvent.clientY - start.y,
            });

        const release = (upEvent: PointerEvent) => {
            element.removeEventListener('pointermove', move);
            element.removeEventListener('pointerup', release);
            element.removeEventListener('pointercancel', release);
            setOffset(null);

            const distance = Math.hypot(
                upEvent.clientX - start.x,
                upEvent.clientY - start.y,
            );

            if (distance >= MIN_DRAG_DISTANCE) {
                wasDraggedRef.current = true;
                onDrop(dropTargetAt(upEvent.clientX, upEvent.clientY, element));
            }
        };

        element.addEventListener('pointermove', move);
        element.addEventListener('pointerup', release);
        element.addEventListener('pointercancel', release);
    };

    return {
        wasDragged: () => wasDraggedRef.current,
        dragProps: {
            onPointerDown,
            style: offset
                ? { translate: `${offset.x}px ${offset.y}px`, zIndex: 10 }
                : undefined,
        },
    };
}
