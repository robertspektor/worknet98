import { describe, expect, it } from 'vite-plus/test';
import { monthDays, weekdayHeads } from './month-days';

const days = (gameTime: Date) => monthDays(gameTime).map((cell) => cell.day);

describe('month days', () => {
    it('starts the month in its weekday column', () => {
        const february = days(new Date(Date.UTC(1998, 1, 3)));

        expect(february.slice(0, 7)).toEqual([
            null,
            null,
            null,
            null,
            null,
            null,
            1,
        ]);
        expect(february[7]).toBe(2);
    });

    it('ends the month on its last day', () => {
        const february = days(new Date(Date.UTC(1998, 1, 3)));

        expect(february.filter((day) => day !== null).at(-1)).toBe(28);
    });

    it('keeps a leap day', () => {
        const february = days(new Date(Date.UTC(1996, 1, 3)));

        expect(february.filter((day) => day !== null).at(-1)).toBe(29);
    });

    it('always fills six weeks', () => {
        expect(monthDays(new Date(Date.UTC(1998, 1, 3)))).toHaveLength(42);
    });

    it('marks the day the game is on', () => {
        const marked = monthDays(new Date(Date.UTC(1998, 1, 3)))
            .filter((cell) => cell.isToday)
            .map((cell) => cell.day);

        expect(marked).toEqual([3]);
    });

    it('names the weekdays from Monday in the player language', () => {
        expect(weekdayHeads('de')).toEqual([
            'Mo',
            'Di',
            'Mi',
            'Do',
            'Fr',
            'Sa',
            'So',
        ]);
    });
});
