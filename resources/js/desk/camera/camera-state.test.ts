import { describe, expect, it } from 'vite-plus/test';
import type { CameraView } from './camera-state';
import { focusedTarget, nextCameraView, ROOM_VIEW } from './camera-state';

const tower: CameraView = { kind: 'detail', target: 'tower' };

describe('camera view', () => {
    it('moves from the room to a thing in it', () => {
        expect(
            nextCameraView(ROOM_VIEW, { type: 'focus', target: 'tower' }),
        ).toEqual(tower);
    });

    it('moves from one thing to the next without going back first', () => {
        expect(
            nextCameraView(tower, { type: 'focus', target: 'calendar' }),
        ).toEqual({
            kind: 'detail',
            target: 'calendar',
        });
    });

    it('goes back to the room', () => {
        expect(nextCameraView(tower, { type: 'room' })).toEqual(ROOM_VIEW);
    });

    it('keeps the view it is already in', () => {
        expect(nextCameraView(ROOM_VIEW, { type: 'room' })).toBe(ROOM_VIEW);
        expect(nextCameraView(tower, { type: 'focus', target: 'tower' })).toBe(
            tower,
        );
    });

    it('tells what is in focus', () => {
        expect(focusedTarget(tower)).toBe('tower');
        expect(focusedTarget(ROOM_VIEW)).toBeNull();
    });
});
