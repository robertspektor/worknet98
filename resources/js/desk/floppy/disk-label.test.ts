import { describe, expect, it } from 'vite-plus/test';
import { diskLabel } from './disk-label';

const t = (key: string) => `translated:${key}`;

describe('diskLabel', () => {
    it('prints the label of a bought disk', () => {
        expect(diskLabel({ slug: 'minefield', kind: 'game' }, t)).toBe(
            'translated:floppy_disk.minefield.label',
        );
    });

    it('leaves a blank disk unlabeled until the player writes on it', () => {
        expect(diskLabel({ slug: 'blank-disks', kind: 'blank' }, t)).toBe('');
        expect(
            diskLabel(
                { slug: 'blank-disks', kind: 'blank', label: 'Taxes 97' },
                t,
            ),
        ).toBe('Taxes 97');
    });
});
