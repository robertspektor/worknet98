import { index as parcelsIndex } from '@/routes/api/v1/parcels';
import { store as unpackingStore } from '@/routes/api/v1/parcels/unpacking';
import { show as playerShow } from '@/routes/api/v1/player';
import { index as shopIndex } from '@/routes/api/v1/shop/floppy-disks';
import { store as orderStore } from '@/routes/api/v1/shop/floppy-disks/orders';
import type { Parcel, Player, ShopItem } from '@/types';
import { getJson, postJson } from '../api/game-api';

export function fetchShopItems(): Promise<ShopItem[]> {
    return getJson<{ data: ShopItem[] }>(shopIndex.url()).then(
        ({ data }) => data,
    );
}

export function fetchBalance(): Promise<number> {
    return getJson<{ data: Player }>(playerShow.url()).then(
        ({ data }) => data.balance,
    );
}

export function orderDisk(item: ShopItem): Promise<ShopItem> {
    return postJson<{ data: ShopItem }>(orderStore.url(item.id)).then(
        ({ data }) => data,
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
