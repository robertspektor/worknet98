import { useEffect, useState } from 'react';
import type { DiskFile, PlayerFloppyDisk } from '@/types';
import { withSavedFile } from './disk-space';
import { eraseDiskFile, fetchDiskFiles, saveDiskFile } from './floppy-disk-api';

type ReadFiles = { diskId: number; files: DiskFile[] };

export function useLoadedDiskFiles(disk: PlayerFloppyDisk | null) {
    const [read, setRead] = useState<ReadFiles | null>(null);
    const diskId = disk?.id ?? null;

    useEffect(() => {
        if (diskId === null) {
            return;
        }

        let isCurrent = true;
        void fetchDiskFiles({ id: diskId })
            .catch(() => [])
            .then((files) => isCurrent && setRead({ diskId, files }));

        return () => {
            isCurrent = false;
        };
    }, [diskId]);

    const updateFiles = (
        id: number,
        change: (files: DiskFile[]) => DiskFile[],
    ) =>
        setRead((current) =>
            current?.diskId === id
                ? { diskId: id, files: change(current.files) }
                : current,
        );

    const saveFile = async (name: string, body: string) => {
        if (!disk) {
            throw new Error('No disk in drive.');
        }

        const saved = await saveDiskFile(disk, name, body);
        updateFiles(disk.id, (files) => withSavedFile(files, saved));
    };

    const eraseFile = async (file: DiskFile) => {
        if (diskId === null) {
            throw new Error('No disk in drive.');
        }

        await eraseDiskFile(file);
        updateFiles(diskId, (files) =>
            files.filter((candidate) => candidate.id !== file.id),
        );
    };

    return {
        files: read !== null && read.diskId === diskId ? read.files : null,
        saveFile,
        eraseFile,
    };
}
