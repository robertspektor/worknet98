import { useTranslation } from '@/i18n/use-translation';
import type { FloppyDiskKind } from '@/types';
import { diskLabel } from '../floppy/disk-label';

type DiskFace = {
    slug: string;
    kind: FloppyDiskKind;
    color: string;
    label?: string | null;
};

export function FloppyDiskArt({ disk }: { disk: DiskFace }) {
    const { t } = useTranslation();

    return (
        <span className={`floppy-disk is-${disk.color} is-${disk.kind}`}>
            <span className="floppy-shutter" aria-hidden="true" />
            <span className="floppy-label">
                <span className="floppy-label-brand" aria-hidden="true">
                    RETROTRON HD
                </span>
                <span className="floppy-label-title">{diskLabel(disk, t)}</span>
            </span>
        </span>
    );
}
