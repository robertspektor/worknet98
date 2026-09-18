import { useEffect } from 'react';
import { store as desktopReached } from '@/routes/api/v1/metrics/desktop-reached';
import { postJson } from '../api/game-api';

export function useDesktopReached(): void {
    useEffect(() => {
        void postJson(desktopReached.url()).catch(() => undefined);
    }, []);
}
