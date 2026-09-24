import { useTranslation } from '@/i18n/use-translation';
import { useComputerMachine } from '../computer/computer-provider';
import { useFloppyDrive } from '../floppy/floppy-drive-provider';
import { sound } from '../sound/sound';
import { FloppyDiskArt } from './floppy-disk-art';
import { PanelTip } from './panel-tip';
import { PowerButton } from './power-button';

/* The optical drive: its name and speed printed on the flap, the seam of
   the tray below them and the eject key next to its light. */

function OpticalBay({ label, speed }: { label: string; speed: string }) {
    return (
        <span className="tower-bay is-optical" aria-hidden="true">
            <span className="bay-label">{label}</span>
            <span className="bay-speed">{speed}</span>
            <span className="bay-tray" />
            <span className="bay-led" />
            <span className="bay-eject" />
        </span>
    );
}

function DriveSlot() {
    const { t } = useTranslation();
    const { disks, drive, eject } = useFloppyDrive();
    const disk = disks.find((candidate) => candidate.id === drive.diskId);
    const isBusy = drive.phase === 'inserting' || drive.phase === 'ejecting';

    return (
        <div className="floppy-drive tower-bay">
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

/* The front panel of a 1998 tower: the power switch with its running light,
   the reset button below it and the status lights above the colour stripe. */

function FrontPanel({ powerLabel }: { powerLabel?: string }) {
    const { t } = useTranslation();
    const computer = useComputerMachine();

    const pressPower = () => {
        sound.unlock();
        computer.togglePower();
    };

    return (
        <div className="tower-panel">
            <PowerButton
                isOn={computer.isOn}
                isHinting={computer.state === 'off'}
                label={powerLabel}
                onPress={pressPower}
            />
            <button
                type="button"
                className="tower-reset"
                aria-label={t('pc_tower.reset')}
                disabled={!computer.isOn}
                onClick={computer.reset}
            >
                <span className="reset-cap" aria-hidden="true" />
                <span className="panel-print" aria-hidden="true">
                    RESET
                </span>
                {computer.isOn && <PanelTip label={t('pc_tower.reset')} />}
            </button>
            <span className="tower-status" aria-hidden="true">
                <span
                    className={`status-led is-drive ${computer.state === 'booting' ? 'is-busy' : ''}`}
                />
                <span
                    className={`status-led is-turbo ${computer.isOn ? 'is-on' : ''}`}
                />
            </span>
            <span className="tower-stripes" aria-hidden="true" />
        </div>
    );
}

export function PcTower({ powerLabel }: { powerLabel?: string }) {
    return (
        <div className="desk-item pc-tower" data-focus="tower">
            <div className="tower-case" data-surface="ledge">
                <span className="tower-side" aria-hidden="true" />
                <OpticalBay label="CD-ROM" speed="52X max" />
                <DriveSlot />
                <div className="tower-lower">
                    <div className="tower-stack">
                        <span className="tower-brand" aria-hidden="true">
                            RETROTRON
                        </span>
                        <span className="tower-vents" aria-hidden="true" />
                    </div>
                    <FrontPanel powerLabel={powerLabel} />
                </div>
            </div>
        </div>
    );
}
