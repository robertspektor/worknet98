export type FloppyDiskKind = 'program' | 'data' | 'story' | 'game';

export type FloppyDisk = {
    id: number;
    slug: string;
    kind: FloppyDiskKind;
    color: string;
    program: string | null;
};
