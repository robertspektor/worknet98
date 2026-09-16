import { useEffect, useState } from 'react';
import type { ShopItem } from '@/types';
import {
    fetchBalance,
    fetchShopItems,
    orderDisk,
} from '../../../floppy/shop-api';
import { useOptionalFloppyDrive } from '../../../floppy/floppy-drive-provider';

function useDeskContentsKey(): string {
    const floppyDrive = useOptionalFloppyDrive();

    return `${floppyDrive?.parcels.length}:${floppyDrive?.disks.length}`;
}

export function useDiskDepot() {
    const [items, setItems] = useState<ShopItem[] | null>(null);
    const [balance, setBalance] = useState<number | null>(null);
    const deskContents = useDeskContentsKey();

    useEffect(() => {
        void fetchShopItems()
            .then(setItems)
            .catch(() => setItems([]));
    }, [deskContents]);

    useEffect(() => {
        void fetchBalance()
            .then(setBalance)
            .catch(() => undefined);
    }, []);

    const order = async (item: ShopItem) => {
        const ordered = await orderDisk(item);
        setItems(
            (current) =>
                current?.map((candidate) =>
                    candidate.id === ordered.id ? ordered : candidate,
                ) ?? null,
        );
        setBalance((current) =>
            current === null ? null : current - item.price,
        );
    };

    return { items, balance, order };
}
