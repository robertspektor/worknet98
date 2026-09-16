import type { CSSProperties, RefObject } from 'react';
import { useEffect, useRef, useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import type { FloppyDisk } from '@/types';
import { useFloppyDrive } from '../floppy/floppy-drive-provider';
import { FloppyDiskArt } from './floppy-disk-art';

function useCloseOnOutsidePointer(
    isOpen: boolean,
    close: () => void,
): RefObject<HTMLDivElement | null> {
    const ref = useRef<HTMLDivElement>(null);

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
    }, [isOpen, close]);

    return ref;
}

function DiskPicker({ onPick }: { onPick: (disk: FloppyDisk) => void }) {
    const { t } = useTranslation();
    const { disks, drive } = useFloppyDrive();
    const isDriveFree = drive.phase === 'empty';

    return (
        <div className="disk-picker" role="menu">
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
    const ref = useCloseOnOutsidePointer(isOpen, () => setOpen(false));
    const stored = disks.filter((disk) => disk.id !== drive.diskId);

    const pick = (disk: FloppyDisk) => {
        setOpen(false);
        insert(disk);
    };

    return (
        <div ref={ref} className="desk-item floppy-box">
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
