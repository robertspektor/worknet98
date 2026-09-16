import { useTranslation } from '@/i18n/use-translation';
import type { Email, EmailFolder } from '@/types';
import { formatDateTime } from '../../ui/format';
import { PixelIcon } from '../../ui/pixel-icon';

export function EmailList({
    emails,
    folder,
    selectedId,
    onSelect,
}: {
    emails: Email[];
    folder: EmailFolder;
    selectedId: number | null;
    onSelect: (email: Email) => void;
}) {
    const { t, locale } = useTranslation();
    const isSent = folder === 'sent';

    return (
        <div className="email-list sunken" role="listbox">
            <div className="email-row email-row-header">
                <span />
                <span>{t(isSent ? 'inbox.to' : 'inbox.from')}</span>
                <span>{t('inbox.subject')}</span>
                <span>{t(isSent ? 'inbox.sent' : 'inbox.received')}</span>
            </div>
            {emails.length === 0 && (
                <p className="email-empty">
                    {t(isSent ? 'inbox.sent_empty' : 'inbox.empty')}
                </p>
            )}
            {emails.map((email) => (
                <button
                    key={email.id}
                    type="button"
                    role="option"
                    aria-selected={email.id === selectedId}
                    className={`email-row ${email.is_read ? '' : 'is-unread'} ${email.id === selectedId ? 'is-selected' : ''}`}
                    onClick={() => onSelect(email)}
                >
                    <PixelIcon name="mail" size={16} />
                    <span>
                        {isSent ? email.recipient_name : email.sender_name}
                    </span>
                    <span>{email.subject}</span>
                    <span>{formatDateTime(email.received_at, locale)}</span>
                </button>
            ))}
        </div>
    );
}
