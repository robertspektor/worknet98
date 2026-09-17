import { useEffect, useState } from 'react';
import type { ShopOffer } from '@/types';
import { useOptionalParcels } from '../../../parcels/parcel-provider';
import { fetchBalance } from '../../../shop/shop-api';
import type { ShopApi } from '../../../shop/shop-api';

export function useShop<Item extends ShopOffer>(api: ShopApi<Item>) {
    const [items, setItems] = useState<Item[] | null>(null);
    const [balance, setBalance] = useState<number | null>(null);
    const parcelCount = useOptionalParcels()?.parcels.length;

    useEffect(() => {
        void api
            .fetchItems()
            .then(setItems)
            .catch(() => setItems([]));
    }, [api, parcelCount]);

    useEffect(() => {
        void fetchBalance()
            .then(setBalance)
            .catch(() => undefined);
    }, []);

    const order = async (item: Item) => {
        const ordered = await api.order(item);
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
