import type { PointerEvent } from 'react';
import { renderScale } from '../screen/viewport';
import { DESKTOP_BOUNDS } from '../screen/viewport';
import type { Position } from './window-state';
import { clamp } from './window-state';

const KEEP_VISIBLE = 60;
const TITLE_BAR_HEIGHT = 20;

export function useWindowDrag(
    position: Position,
    width: number,
    onMove: (position: Position) => void,
) {
    return (event: PointerEvent<HTMLElement>) => {
        if (
            event.button !== 0 ||
            (event.target as HTMLElement).closest('button')
        ) {
            return;
        }

        const titleBar = event.currentTarget;
        const scale = renderScale(
            titleBar.closest<HTMLElement>('.os') ?? titleBar,
        );
        const start = { x: event.clientX, y: event.clientY };
        titleBar.setPointerCapture(event.pointerId);

        const move = (moveEvent: globalThis.PointerEvent) =>
            onMove({
                x: clamp(
                    position.x + (moveEvent.clientX - start.x) / scale,
                    KEEP_VISIBLE - width,
                    DESKTOP_BOUNDS.width - KEEP_VISIBLE,
                ),
                y: clamp(
                    position.y + (moveEvent.clientY - start.y) / scale,
                    0,
                    DESKTOP_BOUNDS.height - TITLE_BAR_HEIGHT,
                ),
            });
        const stop = () => {
            titleBar.removeEventListener('pointermove', move);
            titleBar.removeEventListener('pointerup', stop);
            titleBar.removeEventListener('pointercancel', stop);
        };

        titleBar.addEventListener('pointermove', move);
        titleBar.addEventListener('pointerup', stop);
        titleBar.addEventListener('pointercancel', stop);
    };
}
