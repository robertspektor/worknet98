import { describe, expect, it } from 'vite-plus/test';
import { timeOptions, upcomingDates } from './calendar-options';

describe('calendar options', () => {
    it('lists upcoming dates across month ends', () => {
        expect(upcomingDates(new Date(2026, 8, 29), 3)).toEqual([
            '2026-09-29',
            '2026-09-30',
            '2026-10-01',
        ]);
    });

    it('offers half-hour steps during office hours', () => {
        const options = timeOptions();

        expect(options.at(0)).toBe('07:00');
        expect(options).toContain('10:00');
        expect(options).toContain('13:30');
        expect(options.at(-1)).toBe('18:00');
    });
});
