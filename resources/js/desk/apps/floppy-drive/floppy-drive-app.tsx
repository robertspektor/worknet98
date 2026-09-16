import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import type { FloppyDisk } from '@/types';
import { useFloppyDrive } from '../../floppy/floppy-drive-provider';
import { DiskFileList } from './disk-file-list';
import type { DiskFile } from './disk-files';
import { filesOn } from './disk-files';
import { DriveNotReady } from './drive-not-ready';
import { ReadmeViewer } from './readme-viewer';
import { SetupProgress } from './setup-progress';
import { useProgramSetup } from './use-program-setup';

function DiskContents({ disk }: { disk: FloppyDisk }) {
    const { t } = useTranslation();
    const setup = useProgramSetup();
    const [isReading, setReading] = useState(false);

    const open = (file: DiskFile) =>
        file.action === 'read' ? setReading(true) : void setup.install(disk);

    if (setup.isInstalling) {
        return <SetupProgress />;
    }

    return (
        <div className="app-pad">
            <p className="drive-volume">
                {t('floppy_drive.volume', {
                    label: t(`floppy_disk.${disk.slug}.label`),
                })}
            </p>
            {isReading ? (
                <ReadmeViewer disk={disk} onClose={() => setReading(false)} />
            ) : (
                <DiskFileList files={filesOn(disk)} onOpen={open} />
            )}
        </div>
    );
}

export function FloppyDriveApp() {
    const { loadedDisk } = useFloppyDrive();

    return loadedDisk ? (
        <DiskContents key={loadedDisk.id} disk={loadedDisk} />
    ) : (
        <DriveNotReady />
    );
}
