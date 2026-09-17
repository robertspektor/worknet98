import { index as parcelsIndex } from '@/routes/api/v1/parcels';
import { store as unpackingStore } from '@/routes/api/v1/parcels/unpacking';
import { show as playerShow } from '@/routes/api/v1/player';
import { index as diskShopIndex } from '@/routes/api/v1/shop/floppy-disks';
import { store as diskOrderStore } from '@/routes/api/v1/shop/floppy-disks/orders';
import { index as hardwareShopIndex } from '@/routes/api/v1/shop/hardware-parts';
import { store as hardwareOrderStore } from '@/routes/api/v1/shop/hardware-parts/orders';
import type {
    DiskShopItem,
    HardwareShopItem,
    Parcel,
    Player,
    ShopOffer,
} from '@/types';
import { getJson, postJson } from '../api/game-api';

export type ShopApi<Item extends ShopOffer> = {
    fetchItems: () => Promise<Item[]>;
    order: (item: Item) => Promise<Item>;
};

function shopApi<Item extends ShopOffer>(
    indexUrl: () => string,
    orderUrl: (id: number) => string,
): ShopApi<Item> {
    return {
        fetchItems: () =>
            getJson<{ data: Item[] }>(indexUrl()).then(({ data }) => data),
        order: (item) =>
            postJson<{ data: Item }>(orderUrl(item.id)).then(
                ({ data }) => data,
            ),
    };
}

export const DISK_DEPOT_API = shopApi<DiskShopItem>(
    () => diskShopIndex.url(),
    (id) => diskOrderStore.url(id),
);

export const CHIP_CITY_API = shopApi<HardwareShopItem>(
    () => hardwareShopIndex.url(),
    (id) => hardwareOrderStore.url(id),
);

export function fetchBalance(): Promise<number> {
    return getJson<{ data: Player }>(playerShow.url()).then(
        ({ data }) => data.balance,
    );
}

export function fetchParcels(): Promise<Parcel[]> {
    return getJson<{ data: Parcel[] }>(parcelsIndex.url()).then(
        ({ data }) => data,
    );
}

export function unpackParcel(parcel: Parcel): Promise<void> {
    return postJson<void>(unpackingStore.url(parcel.id));
}
