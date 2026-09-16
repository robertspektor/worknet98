import { useTranslation } from '@/i18n/use-translation';
import type { ShopItem, ShopItemStatus } from '@/types';

const STATUS_KEYS: Record<Exclude<ShopItemStatus, 'available'>, string> = {
    ordered: 'diskdepot.ordered',
    delivered: 'diskdepot.delivered',
    owned: 'diskdepot.owned',
};

export function ShopItemCard({
    item,
    onOrder,
}: {
    item: ShopItem;
    onOrder: (item: ShopItem) => void;
}) {
    const { t } = useTranslation();

    return (
        <li className="shop-item">
            <span
                className={`shop-item-disk is-${item.color}`}
                aria-hidden="true"
            >
                <span className="shop-item-shutter" />
                <span className="shop-item-label" />
            </span>
            <div className="shop-item-body">
                <h3 className="shop-item-name">
                    {t(`floppy_disk.${item.slug}.label`)}
                </h3>
                <p className="shop-item-description">
                    {t(`floppy_disk.${item.slug}.description`)}
                </p>
            </div>
            <div className="shop-item-buy">
                <span className="shop-item-price">
                    {t('diskdepot.price', { amount: item.price })}
                </span>
                {item.status === 'available' ? (
                    <button
                        type="button"
                        className="button"
                        onClick={() => onOrder(item)}
                    >
                        {t('diskdepot.order')}
                    </button>
                ) : (
                    <span className={`shop-item-status is-${item.status}`}>
                        {t(STATUS_KEYS[item.status])}
                    </span>
                )}
            </div>
        </li>
    );
}
