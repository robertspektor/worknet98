export type FloppyDiskKind = 'program' | 'data' | 'story' | 'game';

export type FloppyDisk = {
    id: number;
    slug: string;
    kind: FloppyDiskKind;
    color: string;
    program: string | null;
};

export type ShopItemStatus = 'available' | 'ordered' | 'delivered' | 'owned';

export type ShopItem = FloppyDisk & {
    price: number;
    status: ShopItemStatus;
    delivers_at: string | null;
};

export type Parcel = {
    id: number;
    floppy_disk: FloppyDisk;
};
