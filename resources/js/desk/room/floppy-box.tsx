import type { CSSProperties, RefObject } from 'react';
import { useEffect, useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import type { FloppyDisk } from '@/types';
import { useFloppyDrive } from '../floppy/floppy-drive-provider';
import { usePlaceable } from '../placement/use-placeable';
import { useKeepInView } from '../ui/use-keep-in-view';
import { FloppyDiskArt } from './floppy-disk-art';

function useCloseOnOutsidePointer(
    ref: RefObject<HTMLDivElement | null>,
    isOpen: boolean,
    close: () => void,
): void {
    useEffect(() => {
        if (!isOpen) {
            return;
        }

        const closeOutside = (event: PointerEvent) => {
            if (!ref.current?.contains(event.target as Node)) {
                close();
            }
        };
        document.addEventListener('pointerdown', closeOutside);

        return () => document.removeEventListener('pointerdown', closeOutside);
    }, [ref, isOpen, close]);
}

function DiskPicker({ onPick }: { onPick: (disk: FloppyDisk) => void }) {
    const { t } = useTranslation();
    const { disks, drive } = useFloppyDrive();
    const isDriveFree = drive.phase === 'empty';
    const { ref, shift } = useKeepInView<HTMLDivElement>();

    return (
        <div
            ref={ref}
            className="disk-picker"
            role="menu"
            style={{ translate: `${shift}px 0` }}
        >
            {disks
                .filter((disk) => disk.id !== drive.diskId)
                .map((disk) => (
                    <button
                        key={disk.id}
                        type="button"
                        role="menuitem"
                        className="disk-picker-item"
                        disabled={!isDriveFree}
                        onClick={() => onPick(disk)}
                    >
                        <FloppyDiskArt disk={disk} />
                    </button>
                ))}
            {!isDriveFree && (
                <span className="desk-item-caption disk-picker-hint">
                    {t('floppy_box.drive_busy')}
                </span>
            )}
        </div>
    );
}

export function FloppyBox() {
    const { t } = useTranslation();
    const { disks, drive, insert } = useFloppyDrive();
    const [isOpen, setOpen] = useState(false);
    const { className, ...placeable } =
        usePlaceable<HTMLDivElement>('floppy-box');
    useCloseOnOutsidePointer(placeable.ref, isOpen, () => setOpen(false));
    const stored = disks.filter((disk) => disk.id !== drive.diskId);

    const pick = (disk: FloppyDisk) => {
        setOpen(false);
        insert(disk);
    };

    return (
        <div className={`desk-item floppy-box ${className}`} {...placeable}>
            {isOpen && <DiskPicker onPick={pick} />}
            <button
                type="button"
                className={`floppy-box-body ${isOpen ? 'is-open' : ''}`}
                aria-expanded={isOpen}
                onClick={() => setOpen(!isOpen)}
            >
                <span className="floppy-box-disks" aria-hidden="true">
                    {stored.map((disk, index) => (
                        <span
                            key={disk.id}
                            className={`floppy-box-disk is-${disk.color}`}
                            style={{ '--stack': index } as CSSProperties}
                        />
                    ))}
                </span>
                <span className="floppy-box-front">
                    <span className="floppy-box-label">
                        {t('floppy_box.label')}
                    </span>
                </span>
            </button>
        </div>
    );
}
