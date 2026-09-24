import { describe, expect, it } from 'vite-plus/test';
import {
    clockHands,
    formatGameDate,
    formatGameStamp,
    formatGameTime,
    gameNow,
    gameToday,
} from './game-clock';

const settings = {
    scale: 7,
    real_epoch: '2026-09-14T00:00:00+00:00',
    game_epoch: '1998-01-05T00:00:00',
};

describe('game clock', () => {
    it('runs one game week per real day', () => {
        const now = gameNow(settings, Date.parse('2026-09-15T00:00:00Z'));

        expect(now.toISOString()).toBe('1998-01-12T00:00:00.000Z');
    });

    it('tells the game date as a local calendar day', () => {
        const today = gameToday(settings, Date.parse('2026-09-14T12:00:00Z'));

        expect([
            today.getFullYear(),
            today.getMonth(),
            today.getDate(),
        ]).toEqual([1998, 0, 8]);
    });

    it('formats the game wall time independent of the browser time zone', () => {
        const now = gameNow(settings, Date.parse('2026-09-14T04:48:00Z'));

        expect(formatGameTime(now, 'de')).toBe('09:36');
    });

    it('names the game day the way a calendar would', () => {
        const now = gameNow(settings, Date.parse('2026-09-15T00:00:00Z'));

        expect(formatGameDate(now, 'de')).toBe('Montag, 12. Januar 1998');
    });

    it('puts the hands of a wall clock where the game time is', () => {
        const nine = new Date(Date.UTC(1998, 0, 12, 9, 30));

        expect(clockHands(nine)).toEqual({ hours: 285, minutes: 180 });
    });

    it('stamps date and time the way a terminal footer does', () => {
        const now = gameNow(settings, Date.parse('2026-09-15T00:00:00Z'));

        expect(formatGameStamp(now, 'de')).toBe('12.01.1998  00:00');
    });
});
