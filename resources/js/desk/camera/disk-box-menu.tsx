import { useTranslation } from '@/i18n/use-translation';
import { diskLabel } from '../floppy/disk-label';
import { groupedDisks } from '../floppy/disk-order';
import { useFloppyDrive } from '../floppy/floppy-drive-provider';
import { FloppyDiskArt } from '../room/floppy-disk-art';
import { MenuNote } from './detail-menu';

/* The open box: every disk the player owns, sorted, with the one in the
   drive named above them. A click sends a disk into the machine. */

export function DiskBoxMenu() {
    const { t } = useTranslation();
    const { disks, drive, insert } = useFloppyDrive();
    const loaded = disks.find((disk) => disk.id === drive.diskId) ?? null;
    const stored = disks.filter((disk) => disk.id !== drive.diskId);
    const isDriveFree = drive.phase === 'empty';

    return (
        <>
            <MenuNote
                text={
                    loaded
                        ? t('floppy_box.in_drive', {
                              disk: diskLabel(loaded, t),
                          })
                        : t('floppy_box.count', { count: stored.length })
                }
            />
            {stored.length === 0 ? (
                <MenuNote text={t('floppy_box.empty')} />
            ) : (
                <div className="disk-shelf">
                    {groupedDisks(stored).map((group) => (
                        <div key={group.kind}>
                            <span className="disk-shelf-heading">
                                {t(`disk_kind.${group.kind}`)}
                            </span>
                            <div className="disk-rack">
                                {group.disks.map((disk) => (
                                    <button
                                        key={disk.id}
                                        type="button"
                                        className="disk-rack-item"
                                        disabled={!isDriveFree}
                                        title={diskLabel(disk, t)}
                                        onClick={() => insert(disk)}
                                    >
                                        <FloppyDiskArt disk={disk} />
                                    </button>
                                ))}
                            </div>
                        </div>
                    ))}
                </div>
            )}
            {!isDriveFree && <MenuNote text={t('floppy_box.drive_busy')} />}
        </>
    );
}
