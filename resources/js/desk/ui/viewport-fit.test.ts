import { describe, expect, it } from 'vite-plus/test';
import { shiftIntoView } from './viewport-fit';

describe('shiftIntoView', () => {
    it('leaves a popup alone that fits', () => {
        expect(shiftIntoView({ left: 100, right: 300 }, 1000, 16)).toBe(0);
    });

    it('moves a popup back from the right edge', () => {
        expect(shiftIntoView({ left: 900, right: 1100 }, 1000, 16)).toBe(-116);
    });

    it('moves a popup away from the left edge', () => {
        expect(shiftIntoView({ left: -40, right: 160 }, 1000, 16)).toBe(56);
    });

    it('prefers the left edge when the popup is wider than the viewport', () => {
        expect(shiftIntoView({ left: 0, right: 1200 }, 1000, 16)).toBe(16);
    });
});
