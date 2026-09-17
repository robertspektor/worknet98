import type {
    CSSProperties,
    DragEvent as ReactDragEvent,
    MouseEvent as ReactMouseEvent,
    PointerEvent as ReactPointerEvent,
} from 'react';
import { useLayoutEffect, useRef, useState } from 'react';
import { usePlacements } from './placement-provider';
import type { Box } from './snap';
import { snapToSurface } from './snap';
import { boxOf, surfacesIn } from './surfaces';

const DRAG_THRESHOLD_PX = 5;

type Drag = {
    pointerId: number;
    startX: number;
    startY: number;
    start: Box;
    dx: number;
    dy: number;
    moved: boolean;
};

type Offset = { left: number; top: number };

function offsetInRoom(element: Element, room: HTMLElement): Offset {
    const box = boxOf(element);
    const roomBox = boxOf(room);

    return { left: box.left - roomBox.left, top: box.top - roomBox.top };
}

function moveDrag(drag: Drag, event: PointerEvent): Drag {
    const dx = event.clientX - drag.startX;
    const dy = event.clientY - drag.startY;

    return {
        ...drag,
        dx,
        dy,
        moved: drag.moved || Math.hypot(dx, dy) > DRAG_THRESHOLD_PX,
    };
}

function droppedBox(drag: Drag): Box {
    return {
        ...drag.start,
        left: drag.start.left + drag.dx,
        top: drag.start.top + drag.dy,
    };
}

export function usePlaceable<T extends HTMLElement>(item: string) {
    const { room, roomSize, revision, placementOf, place } = usePlacements();
    const ref = useRef<T>(null);
    const suppressClick = useRef(false);
    const [drag, setDrag] = useState<Drag | null>(null);
    const dragRef = useRef<Drag | null>(null);
    const [parentOffset, setParentOffset] = useState<Offset | null>(null);
    const placement = placementOf(item);
    const isDragging = drag?.moved === true;

    useLayoutEffect(() => {
        const element = ref.current;

        if (element && room) {
            setParentOffset(offsetInRoom(element.offsetParent ?? room, room));
        }
    }, [room, roomSize, revision, isDragging]);

    const onPointerDown = (event: ReactPointerEvent<T>) => {
        const element = ref.current;

        if (event.button !== 0 || !element || !room) {
            return;
        }

        const track = (next: Drag | null) => {
            dragRef.current = next;
            setDrag(next);
        };

        const onMove = (moveEvent: PointerEvent) => {
            if (moveEvent.pointerId === event.pointerId && dragRef.current) {
                track(moveDrag(dragRef.current, moveEvent));
            }
        };

        const onUp = (upEvent: PointerEvent) => {
            if (upEvent.pointerId !== event.pointerId || !dragRef.current) {
                return;
            }

            window.removeEventListener('pointermove', onMove);
            window.removeEventListener('pointerup', onUp);
            window.removeEventListener('pointercancel', onUp);

            const finished = moveDrag(dragRef.current, upEvent);
            suppressClick.current = finished.moved;
            track(null);

            if (finished.moved) {
                place(
                    item,
                    snapToSurface(
                        droppedBox(finished),
                        surfacesIn(room, element),
                        boxOf(room),
                    ),
                );
            }
        };

        suppressClick.current = false;
        track({
            pointerId: event.pointerId,
            startX: event.clientX,
            startY: event.clientY,
            start: boxOf(element),
            dx: 0,
            dy: 0,
            moved: false,
        });
        window.addEventListener('pointermove', onMove);
        window.addEventListener('pointerup', onUp);
        window.addEventListener('pointercancel', onUp);
    };

    const onClickCapture = (event: ReactMouseEvent<T>) => {
        if (suppressClick.current) {
            event.preventDefault();
            event.stopPropagation();
            suppressClick.current = false;
        }
    };

    const roomBox = room ? boxOf(room) : null;
    const isPlaced =
        !isDragging &&
        placement !== null &&
        parentOffset !== null &&
        roomSize.width > 0;

    const style: CSSProperties | undefined =
        isDragging && parentOffset && roomBox
            ? {
                  left:
                      droppedBox(drag).left - roomBox.left - parentOffset.left,
                  top: droppedBox(drag).top - roomBox.top - parentOffset.top,
                  right: 'auto',
                  bottom: 'auto',
              }
            : isPlaced
              ? {
                    left: placement.x * roomSize.width - parentOffset.left,
                    top: placement.y * roomSize.height - parentOffset.top,
                    right: 'auto',
                    bottom: 'auto',
                }
              : undefined;

    return {
        ref,
        className: `is-placeable ${isDragging ? 'is-dragging' : isPlaced ? 'is-placed' : ''}`,
        style,
        onPointerDown,
        onClickCapture,
        onDragStart: (event: ReactDragEvent<T>) => event.preventDefault(),
        draggable: false,
    };
}
