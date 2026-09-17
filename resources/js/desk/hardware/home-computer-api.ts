import { show } from '@/routes/api/v1/home-computer';
import { store as installationStore } from '@/routes/api/v1/home-computer/desk-parts/installation';
import { store as thermalPasteStore } from '@/routes/api/v1/home-computer/thermal-paste';
import type { DeskPart, HomeComputer } from '@/types';
import { getJson, postJson } from '../api/game-api';

export function fetchHomeComputer(): Promise<HomeComputer> {
    return getJson<{ data: HomeComputer }>(show.url()).then(({ data }) => data);
}

export function installDeskPart(
    part: DeskPart,
    thermalPasteApplied: boolean,
): Promise<HomeComputer> {
    return postJson<{ data: HomeComputer }>(installationStore.url(part.id), {
        thermal_paste_applied: thermalPasteApplied,
    }).then(({ data }) => data);
}

export function applyThermalPaste(): Promise<HomeComputer> {
    return postJson<{ data: HomeComputer }>(thermalPasteStore.url()).then(
        ({ data }) => data,
    );
}
