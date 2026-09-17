import type { GameClockSettings } from '@/types/game-clock';
import type { Player, SessionStatus } from '@/types/player';

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            player: Player | null;
            locale: string;
            locales: Record<string, string>;
            translations: Record<string, string>;
            status: SessionStatus;
            gameClock: GameClockSettings;
            [key: string]: unknown;
        };
    }
}
