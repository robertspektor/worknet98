import { usePage } from '@inertiajs/react';
import type { GameClockSettings } from '@/types';

export function useGameClock(): GameClockSettings {
    return usePage().props.gameClock;
}
