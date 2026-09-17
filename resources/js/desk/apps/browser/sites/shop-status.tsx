import { useTranslation } from '@/i18n/use-translation';
import type { ShopOffer, Storefront } from '@/types';

export function ShopBuyBox<Item extends ShopOffer>({
    storefront,
    item,
    onOrder,
}: {
    storefront: Storefront;
    item: Item;
    onOrder: (item: Item) => void;
}) {
    const { t } = useTranslation();

    return (
        <div className="shop-item-buy">
            <span className="shop-item-price">
                {t(`${storefront}.price`, { amount: item.price })}
            </span>
            {item.status === 'available' ? (
                <button
                    type="button"
                    className="button"
                    onClick={() => onOrder(item)}
                >
                    {t(`${storefront}.order`)}
                </button>
            ) : (
                <span className={`shop-item-status is-${item.status}`}>
                    {t(`${storefront}.${item.status}`)}
                </span>
            )}
        </div>
    );
}
