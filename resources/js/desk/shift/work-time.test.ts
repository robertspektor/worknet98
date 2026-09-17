import { describe, expect, it } from 'vite-plus/test';
import { countdownUntil, formatWorkTime, summarizeWorkTime } from './work-time';

describe('work time', () => {
    it('tells the remaining time below the target', () => {
        expect(summarizeWorkTime(4620, 7200)).toEqual({
            workedSeconds: 4620,
            targetSeconds: 7200,
            remainingSeconds: 2580,
            overtimeSeconds: 0,
            progress: 4620 / 7200,
        });
    });

    it('turns time beyond the target into overtime', () => {
        expect(summarizeWorkTime(8400, 7200)).toMatchObject({
            remainingSeconds: 0,
            overtimeSeconds: 1200,
            progress: 1,
        });
    });

    it('formats seconds as hours and minutes', () => {
        expect(formatWorkTime(0)).toBe('0:00');
        expect(formatWorkTime(4659)).toBe('1:17');
        expect(formatWorkTime(36_000)).toBe('10:00');
    });

    it('counts down to a moment in whole minutes', () => {
        const now = Date.parse('2026-09-17T10:00:00Z');

        expect(countdownUntil(Date.parse('2026-09-20T04:57:56Z'), now)).toEqual(
            { days: 2, hours: 18, minutes: 58 },
        );
        expect(countdownUntil(now - 1000, now)).toEqual({
            days: 0,
            hours: 0,
            minutes: 0,
        });
    });
});
