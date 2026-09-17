import { useTranslation } from '@/i18n/use-translation';
import type { ShopOffer, Storefront } from '@/types';
import { GameApiError } from '../../../api/game-api';
import { useDialogs } from '../../../dialogs/dialog-provider';
import { sound } from '../../../sound/sound';
import type { IconName } from '../../../ui/pixel-art';

export function useOrderFlow<Item extends ShopOffer>({
    storefront,
    icon,
    itemLabel,
    order,
}: {
    storefront: Storefront;
    icon: IconName;
    itemLabel: (item: Item) => string;
    order: (item: Item) => Promise<void>;
}) {
    const { t } = useTranslation();
    const dialogs = useDialogs();

    return async (item: Item) => {
        const label = itemLabel(item);
        const confirmed = await dialogs.confirm({
            title: t(`${storefront}.confirm_title`),
            message: t(`${storefront}.confirm_message`, {
                item: label,
                price: item.price,
            }),
            icon,
            confirmLabel: t(`${storefront}.confirm`),
            cancelLabel: t('dialog.cancel'),
        });

        if (!confirmed) {
            return;
        }

        try {
            await order(item);
            await dialogs.alert({
                title: t(`${storefront}.order_placed_title`),
                message: t(`${storefront}.order_placed_message`, {
                    item: label,
                }),
                icon: 'info',
            });
        } catch (error) {
            sound.error();
            await dialogs.alert({
                title: t(`${storefront}.order_failed_title`),
                message:
                    error instanceof GameApiError
                        ? error.message
                        : t('program_setup.failed'),
                icon: 'warning',
            });
        }
    };
}
