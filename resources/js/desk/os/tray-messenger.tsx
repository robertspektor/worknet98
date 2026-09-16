import { useTranslation } from '@/i18n/use-translation';
import { useMessenger } from '../messenger/messenger-provider';
import { PixelIcon } from '../ui/pixel-icon';
import { useWindowManager } from '../windows/window-manager';

export function TrayMessenger() {
    const { t } = useTranslation();
    const messenger = useMessenger();
    const windows = useWindowManager();

    if (messenger.unreadCount === 0) {
        return null;
    }

    const label = t('messenger.unread', { count: messenger.unreadCount });

    return (
        <button
            type="button"
            className="tray-button tray-messenger"
            title={label}
            aria-label={label}
            onClick={() => windows.open('messenger')}
        >
            <PixelIcon name="messenger" size={16} />
        </button>
    );
}
