import { useTranslation } from '@/i18n/use-translation';
import { useCamera, useIsFocused } from '../camera/camera-provider';
import { useFloppyDrive } from '../floppy/floppy-drive-provider';
import { usePlaceable } from '../placement/use-placeable';
import { FloppyBoxArt } from './floppy-box-art';

/* The box of disks on the desk. From across the room it is a thing to step
   up to; up close the lid comes off and the disks are laid out beside it. */

export function FloppyBox() {
    const { t } = useTranslation();
    const { disks, drive } = useFloppyDrive();
    const { focus } = useCamera();
    const isOpen = useIsFocused('disk-box');
    const { className, ...placeable } =
        usePlaceable<HTMLDivElement>('floppy-box');
    const stored = disks.filter((disk) => disk.id !== drive.diskId);

    return (
        <div
            className={`desk-item floppy-box ${className}`}
            data-focus="disk-box"
            {...placeable}
        >
            <button
                type="button"
                className={`floppy-box-body ${isOpen ? 'is-open' : ''}`}
                onClick={() => focus('disk-box')}
            >
                <FloppyBoxArt
                    colors={stored.map((disk) => disk.color)}
                    label={t('floppy_box.label')}
                />
            </button>
        </div>
    );
}
