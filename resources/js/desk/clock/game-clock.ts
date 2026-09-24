import type { GameClockSettings } from '@/types';

export function gameNow(settings: GameClockSettings, realMs: number): Date {
    const realEpochMs = Date.parse(settings.real_epoch);
    const gameEpochMs = Date.parse(`${settings.game_epoch}Z`);

    return new Date(gameEpochMs + (realMs - realEpochMs) * settings.scale);
}

export function gameToday(settings: GameClockSettings, realMs: number): Date {
    const now = gameNow(settings, realMs);

    return new Date(now.getUTCFullYear(), now.getUTCMonth(), now.getUTCDate());
}

export function formatGameTime(gameTime: Date, locale: string): string {
    return gameTime.toLocaleTimeString(locale, {
        hour: '2-digit',
        minute: '2-digit',
        timeZone: 'UTC',
    });
}

export function formatGameDate(gameTime: Date, locale: string): string {
    return gameTime.toLocaleDateString(locale, {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        timeZone: 'UTC',
    });
}

/* Angles of the two hands on a round dial, measured from twelve. */

export function clockHands(gameTime: Date): { hours: number; minutes: number } {
    const minutes = gameTime.getUTCMinutes();

    return {
        hours: ((gameTime.getUTCHours() % 12) * 60 + minutes) * 0.5,
        minutes: minutes * 6,
    };
}

export function formatGameStamp(gameTime: Date, locale: string): string {
    const date = gameTime.toLocaleDateString(locale, {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        timeZone: 'UTC',
    });

    return `${date}  ${formatGameTime(gameTime, locale)}`;
}

export function formatGameMonth(gameTime: Date, locale: string): string {
    return gameTime.toLocaleDateString(locale, {
        month: 'long',
        year: 'numeric',
        timeZone: 'UTC',
    });
}
