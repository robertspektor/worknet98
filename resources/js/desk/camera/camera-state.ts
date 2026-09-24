export type FocusTarget = 'monitor' | 'tower' | 'calendar' | 'disk-box';

export type CameraView =
    | { kind: 'room' }
    | { kind: 'detail'; target: FocusTarget };

export type CameraMove =
    | { type: 'focus'; target: FocusTarget }
    | { type: 'room' };

export const ROOM_VIEW: CameraView = { kind: 'room' };

/* The camera knows two views: the room as a whole, and one thing in it seen
   up close. Moving to the view it is already in changes nothing. */

export function nextCameraView(view: CameraView, move: CameraMove): CameraView {
    if (move.type === 'room') {
        return view.kind === 'room' ? view : ROOM_VIEW;
    }

    return view.kind === 'detail' && view.target === move.target
        ? view
        : { kind: 'detail', target: move.target };
}

export function focusedTarget(view: CameraView): FocusTarget | null {
    return view.kind === 'detail' ? view.target : null;
}
