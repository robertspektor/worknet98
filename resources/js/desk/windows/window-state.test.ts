import { describe, expect, it } from 'vite-plus/test';
import type { WindowAction, WindowState } from './window-state';
import {
    focusedWindowId,
    initialWindowState,
    windowReducer,
} from './window-state';

const bounds = { width: 800, height: 570 };
const size = { width: 400, height: 300 };

function run(...actions: WindowAction[]): WindowState {
    return actions.reduce(windowReducer, initialWindowState);
}

const open = (id: string): WindowAction => ({ type: 'open', id, size, bounds });

describe('windowReducer', () => {
    it('opens windows in a cascade and focuses the newest', () => {
        const state = run(open('a'), open('b'));

        expect(state.windows.map((entry) => entry.position)).toEqual([
            { x: 110, y: 16 },
            { x: 134, y: 40 },
        ]);
        expect(focusedWindowId(state)).toBe('b');
    });

    it('keeps new windows inside the screen', () => {
        const state = run({
            type: 'open',
            id: 'huge',
            size: { width: 1200, height: 900 },
            bounds,
        });

        expect(state.windows[0]).toMatchObject({
            size: bounds,
            position: { x: 0, y: 0 },
        });
    });

    it('raises an already open window instead of opening it twice', () => {
        const state = run(open('a'), open('b'), open('a'));

        expect(state.windows).toHaveLength(2);
        expect(focusedWindowId(state)).toBe('a');
    });

    it('restores minimized windows when they are focused again', () => {
        const minimized = run(open('a'), open('b'), {
            type: 'minimize',
            id: 'b',
        });
        expect(focusedWindowId(minimized)).toBe('a');

        const restored = windowReducer(minimized, { type: 'focus', id: 'b' });
        expect(focusedWindowId(restored)).toBe('b');
    });

    it('closes and moves windows', () => {
        const state = run(
            open('a'),
            open('b'),
            { type: 'move', id: 'a', position: { x: 5, y: 6 } },
            { type: 'close', id: 'b' },
        );

        expect(state.windows).toHaveLength(1);
        expect(state.windows[0].position).toEqual({ x: 5, y: 6 });
        expect(focusedWindowId(state)).toBe('a');
    });

    it('has no focused window when all are minimized', () => {
        const state = run(open('a'), { type: 'minimize', id: 'a' });

        expect(focusedWindowId(state)).toBeNull();
    });
});
