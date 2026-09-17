import { describe, expect, it } from 'vite-plus/test';
import { toTextFileName } from './file-name';

describe('toTextFileName', () => {
    it('turns a typed name into an uppercase text file name', () => {
        expect(toTextFileName(' letter ')).toBe('LETTER.TXT');
        expect(toTextFileName('taxes_97.txt')).toBe('TAXES_97.TXT');
    });

    it('rejects names that do not fit on a disk', () => {
        expect(toTextFileName('')).toBeNull();
        expect(toTextFileName('application')).toBeNull();
        expect(toTextFileName('my note')).toBeNull();
        expect(toTextFileName('virus.exe')).toBeNull();
    });
});
