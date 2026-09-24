import { useTranslation } from '@/i18n/use-translation';
import { useComputerMachine } from '../computer/computer-provider';
import { useFloppyDrive } from '../floppy/floppy-drive-provider';
import { useWorkbench } from '../hardware/workbench-provider';
import { sound } from '../sound/sound';
import { MenuAction } from './detail-menu';

/* Everything the machine itself can be told to do, next to it instead of on
   its front panel. */

export function TowerMenu() {
    const { t } = useTranslation();
    const computer = useComputerMachine();
    const { drive, eject } = useFloppyDrive();
    const workbench = useWorkbench();

    return (
        <>
            <MenuAction
                label={t(
                    computer.isOn ? 'pc_tower.turn_off' : 'pc_tower.turn_on',
                )}
                onSelect={() => {
                    sound.unlock();
                    computer.togglePower();
                }}
            />
            <MenuAction
                label={t('pc_tower.reset')}
                isDisabled={!computer.isOn}
                onSelect={computer.reset}
            />
            <MenuAction
                label={t('pc_tower.eject')}
                isDisabled={drive.phase !== 'loaded'}
                onSelect={eject}
            />
            <MenuAction label={t('workbench.open')} onSelect={workbench.open} />
        </>
    );
}
