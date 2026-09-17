import { destroy as diskFileDestroy } from '@/routes/api/v1/disk-files';
import { store as installationStore } from '@/routes/api/v1/disk-files/installation';
import { index as floppyDisksIndex } from '@/routes/api/v1/floppy-disks';
import {
    index as diskFilesIndex,
    store as diskFilesStore,
} from '@/routes/api/v1/floppy-disks/files';
import { update as labelUpdate } from '@/routes/api/v1/floppy-disks/label';
import { index as installedProgramsIndex } from '@/routes/api/v1/installed-programs';
import type { DiskFile, PlayerFloppyDisk } from '@/types';
import { deleteJson, getJson, postJson, putJson } from '../api/game-api';

export function fetchFloppyDisks(): Promise<PlayerFloppyDisk[]> {
    return getJson<{ data: PlayerFloppyDisk[] }>(floppyDisksIndex.url()).then(
        ({ data }) => data,
    );
}

export function fetchDiskFiles(
    disk: Pick<PlayerFloppyDisk, 'id'>,
): Promise<DiskFile[]> {
    return getJson<{ data: DiskFile[] }>(diskFilesIndex.url(disk.id)).then(
        ({ data }) => data,
    );
}

export function saveDiskFile(
    disk: PlayerFloppyDisk,
    name: string,
    body: string,
): Promise<DiskFile> {
    return postJson<{ data: DiskFile }>(diskFilesStore.url(disk.id), {
        name,
        body,
    }).then(({ data }) => data);
}

export function eraseDiskFile(file: DiskFile): Promise<void> {
    return deleteJson(diskFileDestroy.url(file.id));
}

export function labelFloppyDisk(
    disk: PlayerFloppyDisk,
    label: string,
): Promise<PlayerFloppyDisk> {
    return putJson<{ data: PlayerFloppyDisk }>(labelUpdate.url(disk.id), {
        label,
    }).then(({ data }) => data);
}

export function fetchInstalledPrograms(): Promise<string[]> {
    return getJson<{ data: string[] }>(installedProgramsIndex.url()).then(
        ({ data }) => data,
    );
}

export function installProgramFrom(file: DiskFile): Promise<string> {
    return postJson<{ data: string }>(installationStore.url(file.id)).then(
        ({ data }) => data,
    );
}
