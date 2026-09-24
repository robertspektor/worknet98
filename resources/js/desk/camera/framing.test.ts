import { describe, expect, it } from 'vite-plus/test';
import { boxInStage, frameBox } from './framing';

const viewport = { width: 1000, height: 800 };

describe('framing', () => {
    it('centres what it frames', () => {
        const frame = frameBox(
            { x: 100, y: 100, width: 200, height: 200 },
            viewport,
            0.8,
        );
        const centre = {
            x: frame.x + 200 * frame.scale,
            y: frame.y + 200 * frame.scale,
        };

        expect(centre).toEqual({ x: 500, y: 400 });
    });

    it('fits the side that runs out of room first', () => {
        const wide = frameBox(
            { x: 0, y: 0, width: 500, height: 100 },
            viewport,
            1,
        );

        expect(wide.scale).toBe(2);
    });

    it('keeps the edge of the room out of the window', () => {
        const corner = frameBox(
            { x: 900, y: 700, width: 100, height: 100 },
            viewport,
            0.5,
        );

        expect(corner.x).toBe(viewport.width * (1 - corner.scale));
        expect(corner.y).toBe(viewport.height * (1 - corner.scale));
    });

    it('never gets closer than the limit', () => {
        const tiny = frameBox(
            { x: 0, y: 0, width: 10, height: 10 },
            viewport,
            1,
        );

        expect(tiny.scale).toBe(3.2);
    });

    it('reads a box through the transform the camera already applies', () => {
        const box = boxInStage(
            { left: 200, top: 100, width: 400, height: 200 },
            { left: 0, top: 0, width: 2000, height: 1600 },
            2,
        );

        expect(box).toEqual({ x: 100, y: 50, width: 200, height: 100 });
    });
});
