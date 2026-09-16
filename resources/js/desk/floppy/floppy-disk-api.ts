import { index as floppyDisksIndex } from '@/routes/api/v1/floppy-disks';
import { store as installationStore } from '@/routes/api/v1/floppy-disks/installation';
import { index as installedProgramsIndex } from '@/routes/api/v1/installed-programs';
import type { FloppyDisk } from '@/types';
import { getJson, postJson } from '../api/game-api';

export function fetchFloppyDisks(): Promise<FloppyDisk[]> {
    return getJson<{ data: FloppyDisk[] }>(floppyDisksIndex.url()).then(
        ({ data }) => data,
    );
}

export function fetchInstalledPrograms(): Promise<string[]> {
    return getJson<{ data: string[] }>(installedProgramsIndex.url()).then(
        ({ data }) => data,
    );
}

export function installProgramFrom(disk: FloppyDisk): Promise<string> {
    return postJson<{ data: string }>(installationStore.url(disk.id)).then(
        ({ data }) => data,
    );
}
