import { describe, expect, it } from 'vite-plus/test';
import { GLYPHS, ICONS, PALETTE } from './pixel-art';
import { pixelRuns } from './pixel-runs';

describe('pixel art', () => {
    it.each(Object.entries(ICONS))('%s is a valid 16x16 icon', (_, rows) => {
        expect(rows).toHaveLength(16);
        rows.forEach((row) => expect(row).toHaveLength(16));
    });

    it.each(Object.entries({ ...ICONS, ...GLYPHS }))(
        '%s only uses palette colors',
        (_, rows) => {
            const colors = new Set(rows.join('').replaceAll('.', ''));
            colors.forEach((color) => expect(PALETTE).toHaveProperty(color));
        },
    );
});

describe('pixelRuns', () => {
    it('merges horizontal pixels of the same color', () => {
        expect(pixelRuns(['.kkw', 'kk..'])).toEqual([
            { color: 'k', x: 1, y: 0, width: 2 },
            { color: 'w', x: 3, y: 0, width: 1 },
            { color: 'k', x: 0, y: 1, width: 2 },
        ]);
    });
});
