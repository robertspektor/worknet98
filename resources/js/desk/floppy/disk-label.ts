import type { FloppyDiskKind } from '@/types';

type LabeledDisk = {
    slug: string;
    kind: FloppyDiskKind;
    label?: string | null;
};

type Translate = (key: string) => string;

export function diskLabel(disk: LabeledDisk, t: Translate): string {
    if (disk.label) {
        return disk.label;
    }

    return disk.kind === 'blank' ? '' : t(`floppy_disk.${disk.slug}.label`);
}
