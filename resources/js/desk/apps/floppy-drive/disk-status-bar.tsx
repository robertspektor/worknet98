import { useTranslation } from '@/i18n/use-translation';
import type { DiskFile, PlayerFloppyDisk } from '@/types';
import { diskSize, usedBytes } from '../../floppy/disk-space';

export function DiskStatusBar({
    disk,
    files,
}: {
    disk: PlayerFloppyDisk;
    files: DiskFile[];
}) {
    const { t, locale } = useTranslation();
    const sized = (bytes: number) => {
        const { unitKey, amount } = diskSize(bytes, locale);

        return t(unitKey, { size: amount });
    };

    return (
        <div className="drive-status">
            <span className="drive-status-cell">
                {t('floppy_drive.usage', {
                    used: sized(usedBytes(files)),
                    capacity: sized(disk.capacity_bytes),
                })}
            </span>
            {disk.is_write_protected && (
                <span className="drive-status-cell">
                    {t('floppy_drive.write_protected')}
                </span>
            )}
        </div>
    );
}
