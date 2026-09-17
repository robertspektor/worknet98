import type { DiskFile } from '@/types';

const BYTES_PER_KB = 1_024;
const BYTES_PER_FLOPPY_MB = 1_024_000;

export type DiskSize = {
    unitKey:
        | 'floppy_drive.size_bytes'
        | 'floppy_drive.size_kb'
        | 'floppy_drive.size_mb';
    amount: string;
};

export function usedBytes(files: DiskFile[]): number {
    return files.reduce((total, file) => total + file.size_bytes, 0);
}

export function diskSize(bytes: number, locale: string): DiskSize {
    if (bytes < BYTES_PER_KB) {
        return {
            unitKey: 'floppy_drive.size_bytes',
            amount: bytes.toLocaleString(locale),
        };
    }

    if (bytes < BYTES_PER_FLOPPY_MB) {
        return {
            unitKey: 'floppy_drive.size_kb',
            amount: Math.round(bytes / BYTES_PER_KB).toLocaleString(locale),
        };
    }

    return {
        unitKey: 'floppy_drive.size_mb',
        amount: (bytes / BYTES_PER_FLOPPY_MB).toLocaleString(locale, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }),
    };
}

export function withSavedFile(files: DiskFile[], saved: DiskFile): DiskFile[] {
    return [...files.filter((file) => file.name !== saved.name), saved].sort(
        (a, b) => a.name.localeCompare(b.name),
    );
}
