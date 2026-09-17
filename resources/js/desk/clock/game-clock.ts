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
