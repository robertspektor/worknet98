import { useTranslation } from '@/i18n/use-translation';
import { useFloppyDrive } from '../floppy/floppy-drive-provider';
import { FloppyDiskArt } from './floppy-disk-art';

function DriveSlot() {
    const { t } = useTranslation();
    const { disks, drive, eject } = useFloppyDrive();
    const disk = disks.find((candidate) => candidate.id === drive.diskId);
    const isBusy = drive.phase === 'inserting' || drive.phase === 'ejecting';

    return (
        <div className="floppy-drive">
            <span className="drive-window" aria-hidden="true">
                {disk && (
                    <span className={`drive-disk is-${drive.phase}`}>
                        <FloppyDiskArt disk={disk} />
                    </span>
                )}
            </span>
            <span
                className={`drive-slot ${drive.phase === 'loaded' ? 'is-loaded' : ''}`}
                aria-hidden="true"
            />
            <span className={`drive-led ${isBusy ? 'is-busy' : ''}`} />
            <button
                type="button"
                className="drive-eject"
                aria-label={t('pc_tower.eject')}
                title={t('pc_tower.eject')}
                disabled={drive.phase !== 'loaded'}
                onClick={eject}
            />
        </div>
    );
}

export function PcTower() {
    return (
        <div className="desk-item pc-tower">
            <div className="tower-case" data-surface="ledge">
                <span className="tower-bay" aria-hidden="true" />
                <DriveSlot />
                <span className="tower-brand" aria-hidden="true">
                    RETROTRON
                </span>
                <span className="tower-vents" aria-hidden="true" />
            </div>
        </div>
    );
}
