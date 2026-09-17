import type { ReactNode } from 'react';
import { createContext, use, useEffect, useReducer, useState } from 'react';
import type { FloppyDisk } from '@/types';
import type { AppId } from '../apps/app-registry';
import { sound } from '../sound/sound';
import type { DriveState } from './drive-state';
import { driveReducer, emptyDrive, loadedDiskId } from './drive-state';
import {
    fetchFloppyDisks,
    fetchInstalledPrograms,
    installProgramFrom,
} from './floppy-disk-api';
import { installedApps } from './installed-apps';

export const DISK_SLIDE_MS = 700;

type FloppyDrive = {
    disks: FloppyDisk[];
    drive: DriveState;
    loadedDisk: FloppyDisk | null;
    installedPrograms: AppId[];
    insert: (disk: FloppyDisk) => void;
    eject: () => void;
    install: (disk: FloppyDisk) => Promise<void>;
    refreshDisks: () => void;
};

const FloppyDriveContext = createContext<FloppyDrive | null>(null);

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

    const insert = (disk: FloppyDisk) => {
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
    const [disks, setDisks] = useState<FloppyDisk[]>([]);
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

    const install = async (disk: FloppyDisk) => {
        const program = await installProgramFrom(disk);
        setPrograms((current) =>
            current.includes(program) ? current : [...current, program],
        );
    };

    return {
        disks,
        refreshDisks,
        installedPrograms: installedApps(programs),
        install,
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
    const { disks, refreshDisks, installedPrograms, install } =
        useDiskBoxContents(isSignedIn);
    const loadedId = loadedDiskId(drive);

    return (
        <FloppyDriveContext
            value={{
                disks,
                drive,
                loadedDisk: disks.find((disk) => disk.id === loadedId) ?? null,
                installedPrograms,
                insert,
                eject,
                install,
                refreshDisks,
            }}
        >
            {children}
        </FloppyDriveContext>
    );
}

export function useOptionalFloppyDrive(): FloppyDrive | null {
    return use(FloppyDriveContext);
}

export function useFloppyDrive(): FloppyDrive {
    const floppyDrive = useOptionalFloppyDrive();

    if (!floppyDrive) {
        throw new Error(
            'useFloppyDrive must be used inside FloppyDriveProvider.',
        );
    }

    return floppyDrive;
}
