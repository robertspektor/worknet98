import { describe, expect, it } from 'vite-plus/test';
import type { DiskFile } from '@/types';
import { diskSize, usedBytes, withSavedFile } from './disk-space';

const file = (name: string, size: number): DiskFile => ({
    id: size,
    name,
    kind: 'text',
    size_bytes: size,
    program: null,
    text: '',
});

describe('usedBytes', () => {
    it('adds up the size of every file', () => {
        expect(usedBytes([file('A.TXT', 100), file('B.TXT', 250)])).toBe(350);
        expect(usedBytes([])).toBe(0);
    });
});

describe('diskSize', () => {
    it('counts small files in bytes', () => {
        expect(diskSize(28, 'en')).toEqual({
            unitKey: 'floppy_drive.size_bytes',
            amount: '28',
        });
    });

    it('counts files up to a megabyte in kilobytes', () => {
        expect(diskSize(318_464, 'de')).toEqual({
            unitKey: 'floppy_drive.size_kb',
            amount: '311',
        });
    });

    it('counts floppy megabytes like the label on the disk', () => {
        expect(diskSize(1_474_560, 'en')).toEqual({
            unitKey: 'floppy_drive.size_mb',
            amount: '1.44',
        });
        expect(diskSize(1_341_440, 'de').amount).toBe('1,31');
    });
});

describe('withSavedFile', () => {
    it('adds a new file in name order', () => {
        const files = withSavedFile(
            [file('README.TXT', 1), file('SETUP.EXE', 2)],
            file('NOTE.TXT', 3),
        );

        expect(files.map(({ name }) => name)).toEqual([
            'NOTE.TXT',
            'README.TXT',
            'SETUP.EXE',
        ]);
    });

    it('replaces a file with the same name', () => {
        const files = withSavedFile([file('NOTE.TXT', 1)], file('NOTE.TXT', 9));

        expect(files).toEqual([file('NOTE.TXT', 9)]);
    });
});
