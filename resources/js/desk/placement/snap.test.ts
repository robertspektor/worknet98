import { describe, expect, it } from 'vite-plus/test';
import type { Surface } from './snap';
import { snapToSurface, stickWhereDropped } from './snap';

const room = { left: 0, top: 0, width: 1000, height: 800 };
const desk: Surface = {
    kind: 'area',
    box: { left: 0, top: 700, width: 1000, height: 100 },
};
const tower: Surface = {
    kind: 'ledge',
    box: { left: 850, top: 400, width: 120, height: 300 },
};

describe('snapToSurface', () => {
    it('keeps an item where it was dropped on the desk', () => {
        const item = { left: 200, top: 680, width: 100, height: 50 };

        expect(snapToSurface(item, [desk, tower], room)).toEqual({
            x: 0.25,
            y: 0.9125,
        });
    });

    it('stands an item on top of the tower when dropped close to it', () => {
        const item = { left: 880, top: 330, width: 60, height: 60 };

        expect(snapToSurface(item, [desk, tower], room)).toEqual({
            x: 0.91,
            y: 0.5,
        });
    });

    it('lets an item fall onto the desk when dropped in the air', () => {
        const item = { left: 400, top: 200, width: 100, height: 50 };

        expect(snapToSurface(item, [desk, tower], room)).toEqual({
            x: 0.45,
            y: 0.875,
        });
    });

    it('keeps an item fully on a narrow ledge', () => {
        const item = { left: 930, top: 330, width: 60, height: 60 };

        expect(snapToSurface(item, [tower], room).x).toBe(0.94);
    });
});

describe('stickWhereDropped', () => {
    it('sticks an item exactly where it was dropped, even in the air', () => {
        const item = { left: 400, top: 200, width: 100, height: 50 };

        expect(stickWhereDropped(item, room)).toEqual({ x: 0.45, y: 0.3125 });
    });

    it('keeps a stuck item inside the room', () => {
        const item = { left: 960, top: -40, width: 100, height: 50 };

        expect(stickWhereDropped(item, room)).toEqual({ x: 0.95, y: 0.0625 });
    });
});
