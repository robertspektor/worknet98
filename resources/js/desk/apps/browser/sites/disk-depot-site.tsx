import { useTranslation } from '@/i18n/use-translation';
import type { ShopItem } from '@/types';
import { GameApiError } from '../../../api/game-api';
import { useDialogs } from '../../../dialogs/dialog-provider';
import { sound } from '../../../sound/sound';
import { AppLoading } from '../../../ui/app-loading';
import { ShopItemCard } from './shop-item-card';
import { useDiskDepot } from './use-disk-depot';

function useOrderFlow(order: (item: ShopItem) => Promise<void>) {
    const { t } = useTranslation();
    const dialogs = useDialogs();

    return async (item: ShopItem) => {
        const disk = t(`floppy_disk.${item.slug}.label`);
        const confirmed = await dialogs.confirm({
            title: t('diskdepot.confirm_title'),
            message: t('diskdepot.confirm_message', {
                disk,
                price: item.price,
            }),
            icon: 'floppy',
            confirmLabel: t('diskdepot.confirm'),
            cancelLabel: t('dialog.cancel'),
        });

        if (!confirmed) {
            return;
        }

        try {
            await order(item);
            await dialogs.alert({
                title: t('diskdepot.order_placed_title'),
                message: t('diskdepot.order_placed_message', { disk }),
                icon: 'info',
            });
        } catch (error) {
            sound.error();
            await dialogs.alert({
                title: t('diskdepot.order_failed_title'),
                message:
                    error instanceof GameApiError
                        ? error.message
                        : t('program_setup.failed'),
                icon: 'warning',
            });
        }
    };
}

export function DiskDepotSite() {
    const { t } = useTranslation();
    const depot = useDiskDepot();
    const startOrder = useOrderFlow(depot.order);

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
