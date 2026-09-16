import { useEffect } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { useMessenger } from '../messenger/messenger-provider';
import { PixelIcon } from '../ui/pixel-icon';
import { useWindowManager } from '../windows/window-manager';

const TOAST_DURATION_MS = 7_000;

export function MessengerToast() {
    const { t } = useTranslation();
    const { arrival, dismissArrival } = useMessenger();
    const windows = useWindowManager();
    const isMessengerFocused = windows.focusedId === 'messenger';

    useEffect(() => {
        if (!arrival) {
            return;
        }

        const timer = setTimeout(
            dismissArrival,
            isMessengerFocused ? 0 : TOAST_DURATION_MS,
        );

        return () => clearTimeout(timer);
    }, [arrival, isMessengerFocused]);

    if (!arrival || isMessengerFocused) {
        return null;
    }

    return (
        <button
            type="button"
            className="messenger-toast"
            onClick={() => {
                dismissArrival();
                windows.open('messenger');
            }}
        >
            <span className="messenger-toast-title">
                <PixelIcon name="messenger" size={16} />
                {t('messenger.toast', { name: arrival.contact_name })}
            </span>
            <span className="messenger-toast-body">{arrival.body}</span>
        </button>
    );
}
