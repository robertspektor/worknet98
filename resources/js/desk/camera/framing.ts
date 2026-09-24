import type { FocusTarget } from './camera-state';

export type Box = { x: number; y: number; width: number; height: number };
export type Rect = { left: number; top: number; width: number; height: number };
export type Viewport = { width: number; height: number };
export type Frame = { x: number; y: number; scale: number };

/* How close the camera may get, so that small things on the desk do not end
   up as a wall of pixels. */

const MAX_SCALE = 3.2;

export const ROOM_FRAME: Frame = { x: 0, y: 0, scale: 1 };

/* Where a thing sits on the untransformed stage, measured through whatever
   transform the camera currently applies. */

export function boxInStage(element: Rect, stage: Rect, scale: number): Box {
    return {
        x: (element.left - stage.left) / scale,
        y: (element.top - stage.top) / scale,
        width: element.width / scale,
        height: element.height / scale,
    };
}

/* The camera stays inside the room: a thing near a wall moves off centre
   rather than pulling the edge of the picture into the window. */

function withinRoom(offset: number, side: number, scale: number): number {
    return Math.min(Math.max(offset, side * (1 - scale)), 0);
}

/* The shot that puts a box in the middle of the window and lets it fill the
   given share of it. */

export function frameBox(box: Box, viewport: Viewport, fill: number): Frame {
    const scale = Math.min(
        (viewport.width * fill) / box.width,
        (viewport.height * fill) / box.height,
        MAX_SCALE,
    );

    return {
        scale,
        x: withinRoom(
            viewport.width / 2 - (box.x + box.width / 2) * scale,
            viewport.width,
            scale,
        ),
        y: withinRoom(
            viewport.height / 2 - (box.y + box.height / 2) * scale,
            viewport.height,
            scale,
        ),
    };
}

type Shot = { selector: string; fill: number };

/* What the camera looks at, and how much of the window it hands over to it.
   The monitor is framed on its screen alone, because that is what the player
   works on. */

export const SHOTS: Record<FocusTarget, Shot> = {
    monitor: { selector: '[data-focus="monitor"] .screen-frame', fill: 1 },
    tower: { selector: '[data-focus="tower"]', fill: 0.86 },
    calendar: { selector: '[data-focus="calendar"]', fill: 0.72 },
    'disk-box': { selector: '[data-focus="disk-box"]', fill: 0.62 },
};
