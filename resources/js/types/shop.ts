import type { FloppyDisk } from './floppy-disks';
import type { HardwarePart } from './hardware';

export type Storefront = 'diskdepot' | 'chipcity';

export type ShopItemStatus = 'available' | 'ordered' | 'delivered' | 'owned';

export type ShopOffer = {
    id: number;
    slug: string;
    price: number;
    status: ShopItemStatus;
    delivers_at: string | null;
};

export type DiskShopItem = FloppyDisk & ShopOffer;

export type HardwareShopItem = HardwarePart & ShopOffer;

export type Parcel =
    | {
          id: number;
          storefront: 'diskdepot';
          product: FloppyDisk & { type: 'floppy_disk' };
      }
    | {
          id: number;
          storefront: 'chipcity';
          product: HardwarePart & { type: 'hardware_part' };
      };
