import { useEffect, useState } from 'react';
import { show } from '@/routes/api/v1/player';
import type { Player } from '@/types';
import { getJson } from './game-api';

export function usePlayerProfile(): Player | null {
    const [player, setPlayer] = useState<Player | null>(null);

    useEffect(() => {
        let isCurrent = true;

        void getJson<{ data: Player }>(show.url()).then(({ data }) => {
            if (isCurrent) {
                setPlayer(data);
            }
        });

        return () => {
            isCurrent = false;
        };
    }, []);

    return player;
}
