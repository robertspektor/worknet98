import { show } from '@/routes/api/v1/home-computer';
import type { HomeComputer } from '@/types';
import { getJson } from '../api/game-api';

export function fetchHomeComputer(): Promise<HomeComputer> {
    return getJson<{ data: HomeComputer }>(show.url()).then(({ data }) => data);
}
