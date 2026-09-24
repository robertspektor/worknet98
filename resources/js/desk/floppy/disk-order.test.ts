import { describe, expect, it } from 'vite-plus/test';
import { groupedDisks, sortedDisks } from './disk-order';

const disk = (kind: string, label: string | null, slug = 'disk') =>
    ({ kind, label, slug }) as Parameters<typeof sortedDisks>[0][number];

describe('disk order', () => {
    it('puts the kinds a player works with in front and empty disks last', () => {
        const sorted = sortedDisks([
            disk('blank', null, 'blank-1'),
            disk('data', 'Notizen'),
            disk('program', 'Notepad'),
        ]);

        expect(sorted.map((one) => one.kind)).toEqual([
            'program',
            'data',
            'blank',
        ]);
    });

    it('sorts by name inside a kind', () => {
        const sorted = sortedDisks([
            disk('data', 'Zeugnis'),
            disk('data', 'Abrechnung'),
            disk('data', 'Miete'),
        ]);

        expect(sorted.map((one) => one.label)).toEqual([
            'Abrechnung',
            'Miete',
            'Zeugnis',
        ]);
    });

    it('falls back to the slug where a disk carries no label', () => {
        const sorted = sortedDisks([
            disk('program', null, 'zip-tool'),
            disk('program', null, 'address-book'),
        ]);

        expect(sorted.map((one) => one.slug)).toEqual([
            'address-book',
            'zip-tool',
        ]);
    });

    it('splits the box into shelves, one per kind', () => {
        const groups = groupedDisks([
            disk('blank', null, 'blank-2'),
            disk('program', 'Notepad'),
            disk('blank', null, 'blank-1'),
        ]);

        expect(groups.map((group) => [group.kind, group.disks.length])).toEqual(
            [
                ['program', 1],
                ['blank', 2],
            ],
        );
    });

    it('leaves the disks it was given alone', () => {
        const disks = [disk('blank', null), disk('program', 'Notepad')];
        sortedDisks(disks);

        expect(disks[0].kind).toBe('blank');
    });
});
