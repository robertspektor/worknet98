import { describe, expect, it } from 'vite-plus/test';
import {
    appLaunchDelayMs,
    biosLineDelayMs,
    memoryCountDurationMs,
    pageLoadDurationMs,
    splashStatusDelayMs,
} from './performance';

describe('performance', () => {
    it('makes the starter processor noticeably slow', () => {
        expect(appLaunchDelayMs(75)).toBe(1500);
        expect(pageLoadDurationMs(75)).toBe(1750);
        expect(memoryCountDurationMs(75)).toBe(1800);
    });

    it('gets faster with every processor upgrade', () => {
        const timings = [75, 133, 200].map((mhz) => [
            biosLineDelayMs(mhz),
            splashStatusDelayMs(mhz),
            appLaunchDelayMs(mhz),
            pageLoadDurationMs(mhz),
        ]);

        timings.slice(1).forEach((faster, index) => {
            faster.forEach((ms, metric) =>
                expect(ms).toBeLessThan(timings[index][metric]),
            );
        });
    });

    it('never gets faster than the turbo processor', () => {
        expect(appLaunchDelayMs(200)).toBe(0);
        expect(appLaunchDelayMs(400)).toBe(0);
        expect(biosLineDelayMs(400)).toBe(biosLineDelayMs(200));
    });
});
