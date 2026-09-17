import { useTranslation } from '@/i18n/use-translation';
import type { FloppyDisk } from '@/types';

type DiskFace = Pick<FloppyDisk, 'slug' | 'kind' | 'color'>;

export function FloppyDiskArt({ disk }: { disk: DiskFace }) {
    const { t } = useTranslation();

    return (
        <span className={`floppy-disk is-${disk.color} is-${disk.kind}`}>
            <span className="floppy-shutter" aria-hidden="true" />
            <span className="floppy-label">
                <span className="floppy-label-brand" aria-hidden="true">
                    RETROTRON HD
                </span>
                <span className="floppy-label-title">
                    {t(`floppy_disk.${disk.slug}.label`)}
                </span>
            </span>
        </span>
    );
}
