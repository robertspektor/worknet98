import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import type { DiskFile, PlayerFloppyDisk } from '@/types';
import { diskLabel } from '../../floppy/disk-label';
import { useFloppyDrive } from '../../floppy/floppy-drive-provider';
import { AppLoading } from '../../ui/app-loading';
import { DiskFileList } from './disk-file-list';
import { DiskStatusBar } from './disk-status-bar';
import { DriveNotReady } from './drive-not-ready';
import { SetupProgress } from './setup-progress';
import { TextFileViewer } from './text-file-viewer';
import { useDiskActions } from './use-disk-actions';
import { useProgramSetup } from './use-program-setup';

function DriveToolbar({
    disk,
    selected,
}: {
    disk: PlayerFloppyDisk;
    selected: DiskFile | null;
}) {
    const { t } = useTranslation();
    const actions = useDiskActions();
    const label = diskLabel(disk, t);

    return (
        <div className="drive-toolbar">
            <p className="drive-volume">
                {label
                    ? t('floppy_drive.volume', { label })
                    : t('floppy_drive.no_label')}
            </p>
            {disk.is_labelable && (
                <button
                    type="button"
                    className="button"
                    onClick={() => void actions.label(disk)}
                >
                    {t('floppy_drive.label')}
                </button>
            )}
            {!disk.is_write_protected && (
                <button
                    type="button"
                    className="button"
                    disabled={!selected}
                    onClick={() => selected && void actions.erase(selected)}
                >
                    {t('floppy_drive.delete')}
                </button>
            )}
        </div>
    );
}

function DiskContents({
    disk,
    files,
}: {
    disk: PlayerFloppyDisk;
    files: DiskFile[];
}) {
    const setup = useProgramSetup();
    const [selectedId, setSelectedId] = useState<number | null>(null);
    const [reading, setReading] = useState<DiskFile | null>(null);
    const selected = files.find((file) => file.id === selectedId) ?? null;

    const open = (file: DiskFile) =>
        file.kind === 'setup' ? void setup.install(file) : setReading(file);

    if (setup.isInstalling) {
        return <SetupProgress />;
    }

    return (
        <div className="app-pad drive-app">
            <DriveToolbar disk={disk} selected={selected} />
            {reading ? (
                <TextFileViewer
                    file={reading}
                    onClose={() => setReading(null)}
                />
            ) : (
                <DiskFileList
                    files={files}
                    selectedId={selectedId}
                    onSelect={(file) => setSelectedId(file.id)}
                    onOpen={open}
                />
            )}
            <DiskStatusBar disk={disk} files={files} />
        </div>
    );
}

export function FloppyDriveApp() {
    const { loadedDisk, loadedFiles } = useFloppyDrive();

    if (!loadedDisk) {
        return <DriveNotReady />;
    }

    return loadedFiles ? (
        <DiskContents
            key={loadedDisk.id}
            disk={loadedDisk}
            files={loadedFiles}
        />
    ) : (
        <AppLoading labelKey="floppy_drive.reading" />
    );
}
