import { useTranslation } from '@/i18n/use-translation';
import { useMailbox } from '../mailbox/mailbox-provider';
import { PixelIcon } from '../ui/pixel-icon';
import { useWindowManager } from '../windows/window-manager';

export function TrayMail() {
    const { t } = useTranslation();
    const mailbox = useMailbox();
    const windows = useWindowManager();

    if (mailbox.unreadCount === 0) {
        return null;
    }

    const label = t('inbox.unread', { count: mailbox.unreadCount });

    return (
        <button
            type="button"
            className="tray-button tray-mail"
            title={label}
            aria-label={label}
            onClick={() => windows.open('mail')}
        >
            <PixelIcon name="mail" size={16} />
        </button>
    );
}
