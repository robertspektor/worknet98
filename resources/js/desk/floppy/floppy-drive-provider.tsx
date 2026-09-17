import type { ReactNode } from 'react';
import { useEffect, useReducer, useState } from 'react';
import type { DiskFile, PlayerFloppyDisk } from '@/types';
import type { AppId } from '../apps/app-registry';
import { sound } from '../sound/sound';
import { createDeskContext } from '../state/create-desk-context';
import type { DriveState } from './drive-state';
import { driveReducer, emptyDrive, loadedDiskId } from './drive-state';
import {
    fetchFloppyDisks,
    fetchInstalledPrograms,
    installProgramFrom,
    labelFloppyDisk,
} from './floppy-disk-api';
import { installedApps } from './installed-apps';
import { useLoadedDiskFiles } from './use-loaded-disk-files';

export const DISK_SLIDE_MS = 700;

type FloppyDrive = {
    disks: PlayerFloppyDisk[];
    drive: DriveState;
    loadedDisk: PlayerFloppyDisk | null;
    loadedFiles: DiskFile[] | null;
    installedPrograms: AppId[];
    insert: (disk: PlayerFloppyDisk) => void;
    eject: () => void;
    install: (setupFile: DiskFile) => Promise<void>;
    saveFile: (name: string, body: string) => Promise<void>;
    eraseFile: (file: DiskFile) => Promise<void>;
    label: (disk: PlayerFloppyDisk, label: string) => Promise<void>;
    refreshDisks: () => void;
};

const { Context, useOptional, useRequired } =
    createDeskContext<FloppyDrive>('FloppyDrive');

function useDriveMechanics() {
    const [drive, dispatch] = useReducer(driveReducer, emptyDrive);

    useEffect(() => {
        if (drive.phase !== 'inserting' && drive.phase !== 'ejecting') {
            return;
        }

        const timer = setTimeout(
            () => dispatch({ type: 'settle' }),
            DISK_SLIDE_MS,
        );

        return () => clearTimeout(timer);
    }, [drive.phase]);

    const insert = (disk: PlayerFloppyDisk) => {
        if (drive.phase === 'empty') {
            sound.floppySeek();
            dispatch({ type: 'insert', diskId: disk.id });
        }
    };

    const eject = () => {
        if (drive.phase === 'loaded') {
            sound.floppyEject();
            dispatch({ type: 'eject' });
        }
    };

    return { drive, insert, eject };
}

function useDiskBoxContents(isSignedIn: boolean) {
    const [disks, setDisks] = useState<PlayerFloppyDisk[]>([]);
    const [programs, setPrograms] = useState<string[]>([]);

    const refreshDisks = () =>
        void fetchFloppyDisks()
            .then(setDisks)
            .catch(() => undefined);

    useEffect(() => {
        if (!isSignedIn) {
            return;
        }

        refreshDisks();
        void fetchInstalledPrograms()
            .then(setPrograms)
            .catch(() => undefined);
    }, [isSignedIn]);

    const install = async (setupFile: DiskFile) => {
        const program = await installProgramFrom(setupFile);
        setPrograms((current) =>
            current.includes(program) ? current : [...current, program],
        );
    };

    const label = async (disk: PlayerFloppyDisk, text: string) => {
        const labeled = await labelFloppyDisk(disk, text);
        setDisks((current) =>
            current.map((candidate) =>
                candidate.id === labeled.id ? labeled : candidate,
            ),
        );
    };

    return {
        disks,
        refreshDisks,
        installedPrograms: installedApps(programs),
        install,
        label,
    };
}

export function FloppyDriveProvider({
    isSignedIn,
    children,
}: {
    isSignedIn: boolean;
    children: ReactNode;
}) {
    const { drive, insert, eject } = useDriveMechanics();
    const { disks, refreshDisks, installedPrograms, install, label } =
        useDiskBoxContents(isSignedIn);
    const loadedId = loadedDiskId(drive);
    const loadedDisk = disks.find((disk) => disk.id === loadedId) ?? null;
    const { files, saveFile, eraseFile } = useLoadedDiskFiles(loadedDisk);

    return (
        <Context
            value={{
                disks,
                drive,
                loadedDisk,
                loadedFiles: files,
                installedPrograms,
                insert,
                eject,
                install,
                saveFile,
                eraseFile,
                label,
                refreshDisks,
            }}
        >
            {children}
        </Context>
    );
}

export const useOptionalFloppyDrive = useOptional;
export const useFloppyDrive = useRequired;
