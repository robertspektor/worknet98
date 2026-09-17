import { useTranslation } from '@/i18n/use-translation';
import type { Email } from '@/types';
import { PromotionOfferPanel } from './promotion-offer-panel';

export function EmailReader({ email }: { email: Email | null }) {
    const { t } = useTranslation();

    if (!email) {
        return (
            <div className="email-reader sunken">
                <p className="email-empty">{t('inbox.select')}</p>
            </div>
        );
    }

    const isSent = email.folder === 'sent';

    return (
        <article key={email.id} className="email-reader sunken">
            <dl className="email-meta">
                <dt>{t('inbox.from')}:</dt>
                <dd>
                    {email.sender_name} &lt;{email.sender_address}&gt;
                </dd>
                {isSent && (
                    <>
                        <dt>{t('inbox.to')}:</dt>
                        <dd>
                            {email.recipient_name} &lt;
                            {email.recipient_address}&gt;
                        </dd>
                    </>
                )}
                <dt>{t('inbox.subject')}:</dt>
                <dd>{email.subject}</dd>
                {email.action && (
                    <>
                        <dt>{t('inbox.action')}:</dt>
                        <dd>{t(`inbox.actions.${email.action}`)}</dd>
                    </>
                )}
            </dl>
            <div className="email-body">{email.body}</div>
            {email.promotion_offer && (
                <PromotionOfferPanel offer={email.promotion_offer} />
            )}
        </article>
    );
}
