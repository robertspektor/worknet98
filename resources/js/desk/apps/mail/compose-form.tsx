import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { index as customersIndex } from '@/routes/api/v1/customers';
import type { Customer, Email, EmailAction } from '@/types';
import { useApiResource } from '../../api/use-api-resource';
import { useCompanySoftware } from '../../company-software/company-software-provider';
import type { OutgoingEmail } from '../../mailbox/mailbox-provider';
import { AppLoading } from '../../ui/app-loading';
import { actionsFor } from './compose-actions';

function replySubject(subject: string): string {
    return subject.startsWith('Re: ') ? subject : `Re: ${subject}`;
}

function ComposeFields({
    customers,
    replyTo,
    onSend,
    onCancel,
}: {
    customers: Customer[];
    replyTo: Email | null;
    onSend: (email: OutgoingEmail) => Promise<boolean>;
    onCancel: () => void;
}) {
    const { t } = useTranslation();
    const [customerId, setCustomerId] = useState(() =>
        String(
            customers.find(
                (customer) =>
                    customer.email_address === replyTo?.sender_address,
            )?.id ?? '',
        ),
    );
    const [subject, setSubject] = useState(
        replyTo ? replySubject(replyTo.subject) : '',
    );
    const [body, setBody] = useState('');
    const actions = actionsFor(useCompanySoftware());
    const [action, setAction] = useState<EmailAction>(actions[0]);
    const [isSending, setSending] = useState(false);
    const isComplete =
        customerId !== '' && subject.trim() !== '' && body.trim() !== '';

    const submit = async () => {
        setSending(true);
        const sent = await onSend({
            customer_id: Number(customerId),
            subject,
            body,
            action,
        }).finally(() => setSending(false));

        if (sent) {
            onCancel();
        }
    };

    return (
        <form
            className="compose-form"
            onSubmit={(event) => {
                event.preventDefault();
                void submit();
            }}
        >
            <label className="compose-field" htmlFor="compose-to">
                <span>{t('inbox.to')}:</span>
                <select
                    id="compose-to"
                    className="select"
                    value={customerId}
                    onChange={(event) => setCustomerId(event.target.value)}
                >
                    <option value="">{t('inbox.choose_recipient')}</option>
                    {customers.map((customer) => (
                        <option key={customer.id} value={customer.id}>
                            {customer.name} &lt;{customer.email_address}&gt;
                        </option>
                    ))}
                </select>
            </label>
            <label className="compose-field" htmlFor="compose-subject">
                <span>{t('inbox.subject')}:</span>
                <input
                    id="compose-subject"
                    className="input"
                    maxLength={120}
                    value={subject}
                    onChange={(event) => setSubject(event.target.value)}
                />
            </label>
            <label className="compose-field" htmlFor="compose-action">
                <span>{t('inbox.action')}:</span>
                <select
                    id="compose-action"
                    className="select"
                    value={action}
                    onChange={(event) =>
                        setAction(event.target.value as EmailAction)
                    }
                >
                    {actions.map((option) => (
                        <option key={option} value={option}>
                            {t(`inbox.actions.${option}`)}
                        </option>
                    ))}
                </select>
            </label>
            <textarea
                className="input compose-body"
                aria-label={t('inbox.body')}
                maxLength={2000}
                value={body}
                onChange={(event) => setBody(event.target.value)}
            />
            <div className="app-actions app-actions-end">
                <button
                    type="submit"
                    className="button button-primary"
                    disabled={isSending || !isComplete}
                >
                    {t('inbox.send')}
                </button>
                <button type="button" className="button" onClick={onCancel}>
                    {t('dialog.cancel')}
                </button>
            </div>
        </form>
    );
}

export function ComposeForm(props: {
    replyTo: Email | null;
    onSend: (email: OutgoingEmail) => Promise<boolean>;
    onCancel: () => void;
}) {
    const { data: customers } = useApiResource<Customer[]>(
        customersIndex.url(),
    );

    return customers ? (
        <ComposeFields customers={customers} {...props} />
    ) : (
        <AppLoading />
    );
}
