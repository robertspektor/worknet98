import { useTranslation } from '@/i18n/use-translation';
import type { HardwareShopItem } from '@/types';
import { CpuChipArt } from '../../../room/cpu-chip-art';
import { CHIP_CITY_API } from '../../../shop/shop-api';
import { AppLoading } from '../../../ui/app-loading';
import { ShopBuyBox } from './shop-status';
import { useOrderFlow } from './use-order-flow';
import { useShop } from './use-shop';

const TURBO_MHZ = 200;

function ChipCityItem({
    item,
    onOrder,
}: {
    item: HardwareShopItem;
    onOrder: (item: HardwareShopItem) => void;
}) {
    const { t } = useTranslation();

    return (
        <li className="shop-item chip-city-item">
            <span className="chip-city-chip" aria-hidden="true">
                <CpuChipArt />
            </span>
            <div className="shop-item-body">
                <h3 className="shop-item-name">
                    {t(`hardware_part.${item.slug}.label`)}
                    {item.speed_mhz >= TURBO_MHZ && (
                        <span className="chip-city-turbo">
                            {t('chipcity.turbo')}
                        </span>
                    )}
                </h3>
                <p className="shop-item-description">
                    {t(`hardware_part.${item.slug}.description`)}
                </p>
                <p className="chip-city-speed">
                    {t('chipcity.speed')}:{' '}
                    <b>{t('chipcity.speed_value', { mhz: item.speed_mhz })}</b>
                </p>
            </div>
            <ShopBuyBox storefront="chipcity" item={item} onOrder={onOrder} />
        </li>
    );
}

export function ChipCitySite() {
    const { t } = useTranslation();
    const shop = useShop(CHIP_CITY_API);
    const startOrder = useOrderFlow({
        storefront: 'chipcity',
        icon: 'computer',
        itemLabel: (item) => t(`hardware_part.${item.slug}.label`),
        order: shop.order,
    });

    return (
        <div className="web-page chip-city">
            <header className="chip-city-banner">
                <span className="chip-city-logo">
                    Chip<b>City</b>
                </span>
                <span className="chip-city-tagline">
                    {t('chipcity.tagline')}
                </span>
            </header>
            <div className="chip-city-account">
                <span>
                    {shop.balance === null
                        ? '...'
                        : t('chipcity.balance', { amount: shop.balance })}
                </span>
                <span className="muted">{t('chipcity.shipping')}</span>
            </div>
            {shop.items === null ? (
                <AppLoading labelKey="chipcity.loading" />
            ) : (
                <ul className="shop-items">
                    {shop.items.map((item) => (
                        <ChipCityItem
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
