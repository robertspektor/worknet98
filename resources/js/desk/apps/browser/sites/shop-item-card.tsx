import { useTranslation } from '@/i18n/use-translation';
import type { DiskShopItem } from '@/types';
import { FloppyDiskArt } from '../../../room/floppy-disk-art';
import { ShopBuyBox } from './shop-status';

export function ShopItemCard({
    item,
    onOrder,
}: {
    item: DiskShopItem;
    onOrder: (item: DiskShopItem) => void;
}) {
    const { t } = useTranslation();

    return (
        <li className="shop-item">
            <span className="shop-item-disk" aria-hidden="true">
                <FloppyDiskArt disk={item} />
            </span>
            <div className="shop-item-body">
                <h3 className="shop-item-name">
                    {t(`floppy_disk.${item.slug}.label`)}
                </h3>
                <p className="shop-item-description">
                    {t(`floppy_disk.${item.slug}.description`)}
                </p>
            </div>
            <ShopBuyBox storefront="diskdepot" item={item} onOrder={onOrder} />
        </li>
    );
}
