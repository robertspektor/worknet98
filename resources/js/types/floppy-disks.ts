export type FloppyDiskKind = 'program' | 'data' | 'story' | 'game' | 'blank';

export type FloppyDisk = {
    id: number;
    slug: string;
    kind: FloppyDiskKind;
    color: string;
    pack_size: number;
};

export type PlayerFloppyDisk = {
    id: number;
    slug: string;
    kind: FloppyDiskKind;
    color: string;
    label: string | null;
    is_labelable: boolean;
    is_write_protected: boolean;
    capacity_bytes: number;
};

export type DiskFileKind = 'text' | 'setup';

export type DiskFile = {
    id: number;
    name: string;
    kind: DiskFileKind;
    size_bytes: number;
    program: string | null;
    text: string | null;
};
