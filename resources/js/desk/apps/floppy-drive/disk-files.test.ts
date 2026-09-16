import { describe, expect, it } from 'vite-plus/test';
import type { FloppyDisk } from '@/types';
import { filesOn } from './disk-files';

const disk: FloppyDisk = {
    id: 1,
    slug: 'backup',
    kind: 'data',
    color: 'red',
    program: null,
};

describe('filesOn', () => {
    it('puts a readme on every disk', () => {
        expect(filesOn(disk).map((file) => file.name)).toEqual(['README.TXT']);
    });

    it('adds a setup program to disks with a program', () => {
        expect(
            filesOn({ ...disk, program: 'minefield' }).map((file) => file.name),
        ).toEqual(['README.TXT', 'SETUP.EXE']);
    });
});
