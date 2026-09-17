import { describe, expect, it } from 'vite-plus/test';
import { isPageLoaded, pageLoadProgress } from './page-load';

describe('pageLoadProgress', () => {
    it('fills up over the load duration', () => {
        expect(pageLoadProgress(0, 2000)).toBe(0);
        expect(pageLoadProgress(500, 2000)).toBe(0.25);
        expect(isPageLoaded(pageLoadProgress(1999, 2000))).toBe(false);
    });

    it('is loaded once the duration has passed or when there is no delay', () => {
        expect(isPageLoaded(pageLoadProgress(2500, 2000))).toBe(true);
        expect(isPageLoaded(pageLoadProgress(0, 0))).toBe(true);
    });
});
