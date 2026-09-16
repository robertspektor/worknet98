import { useTranslation } from '@/i18n/use-translation';
import type { FloppyDisk } from '@/types';

export function FloppyDiskArt({ disk }: { disk: FloppyDisk }) {
    const { t } = useTranslation();

    return (
        <span className={`floppy-disk is-${disk.color}`}>
            <span className="floppy-shutter" aria-hidden="true" />
            <span className="floppy-label">
                {t(`floppy_disk.${disk.slug}.label`)}
            </span>
        </span>
    );
}
