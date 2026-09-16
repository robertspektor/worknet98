import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';

const MESSAGE_MAX_LENGTH = 1000;

export function ApplicationForm({
    onSubmit,
}: {
    onSubmit: (message: string) => Promise<void>;
}) {
    const { t } = useTranslation();
    const [message, setMessage] = useState('');
    const [isSending, setSending] = useState(false);

    const submit = async () => {
        setSending(true);
        await onSubmit(message).finally(() => setSending(false));
    };

    return (
        <form
            className="application-form"
            onSubmit={(event) => {
                event.preventDefault();
                void submit();
            }}
        >
            <label className="field" htmlFor="worknet-message">
                <span>{t('worknet.message_label')}</span>
                <textarea
                    id="worknet-message"
                    className="input application-message"
                    rows={3}
                    maxLength={MESSAGE_MAX_LENGTH}
                    placeholder={t('worknet.message_placeholder')}
                    value={message}
                    onChange={(event) => setMessage(event.target.value)}
                />
            </label>
            <div className="app-actions app-actions-end">
                <button
                    type="submit"
                    className="button button-primary"
                    disabled={isSending}
                >
                    {t(isSending ? 'worknet.sending' : 'worknet.apply')}
                </button>
            </div>
        </form>
    );
}
