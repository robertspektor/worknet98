import { useTranslation } from '@/i18n/use-translation';
import { DISK_DEPOT_API } from '../../../shop/shop-api';
import { AppLoading } from '../../../ui/app-loading';
import { ShopItemCard } from './shop-item-card';
import { useOrderFlow } from './use-order-flow';
import { useShop } from './use-shop';

export function DiskDepotSite() {
    const { t } = useTranslation();
    const depot = useShop(DISK_DEPOT_API);
    const startOrder = useOrderFlow({
        storefront: 'diskdepot',
        icon: 'floppy',
        itemLabel: (item) => t(`floppy_disk.${item.slug}.label`),
        order: depot.order,
    });

    return (
        <div className="web-page disk-depot">
            <header className="disk-depot-banner">
                <span className="disk-depot-logo">
                    Disk<b>Depot</b>
                </span>
                <span className="disk-depot-tagline">
                    {t('diskdepot.tagline')}
                </span>
            </header>
            <div className="disk-depot-account">
                <span>
                    {depot.balance === null
                        ? '...'
                        : t('diskdepot.balance', { amount: depot.balance })}
                </span>
                <span className="muted">{t('diskdepot.shipping')}</span>
            </div>
            {depot.items === null ? (
                <AppLoading labelKey="diskdepot.loading" />
            ) : (
                <ul className="shop-items">
                    {depot.items.map((item) => (
                        <ShopItemCard
                            key={item.id}
                            item={item}
                            onOrder={(next) => void startOrder(next)}
                        />
                    ))}
                </ul>
            )}
        </div>
    );
}
