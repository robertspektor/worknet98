import type { FloppyDiskKind } from '@/types';

type Sortable = { kind: FloppyDiskKind; label: string | null; slug: string };

/* The order the disks stand in the box: what a player reaches for most sits
   in front, empty disks wait at the back. */

const GROUPS: Record<FloppyDiskKind, number> = {
    program: 0,
    game: 1,
    story: 2,
    data: 3,
    blank: 4,
};

function nameOf(disk: Sortable): string {
    return (disk.label ?? disk.slug).toLocaleLowerCase();
}

export function sortedDisks<T extends Sortable>(disks: T[]): T[] {
    return [...disks].sort(
        (one, other) =>
            GROUPS[one.kind] - GROUPS[other.kind] ||
            nameOf(one).localeCompare(nameOf(other)),
    );
}

export type DiskGroup<T> = { kind: FloppyDiskKind; disks: T[] };

/* The same order, but split into the shelves of the box, so a collection
   that has grown stays readable. */

export function groupedDisks<T extends Sortable>(disks: T[]): DiskGroup<T>[] {
    return sortedDisks(disks).reduce<DiskGroup<T>[]>((groups, disk) => {
        const last = groups.at(-1);

        if (last?.kind === disk.kind) {
            last.disks.push(disk);

            return groups;
        }

        return [...groups, { kind: disk.kind, disks: [disk] }];
    }, []);
}
