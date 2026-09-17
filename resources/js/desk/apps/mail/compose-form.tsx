import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { index as colleaguesIndex } from '@/routes/api/v1/colleagues';
import { index as customersIndex } from '@/routes/api/v1/customers';
import type { Colleague, Customer, Email, EmailAction } from '@/types';
import { useApiResource } from '../../api/use-api-resource';
import { useCompanySoftware } from '../../company-software/company-software-provider';
import type { OutgoingEmail } from '../../mailbox/mailbox-provider';
import { AppLoading } from '../../ui/app-loading';
import { actionsFor } from './compose-actions';
import { recipientFields, recipientsOf } from './compose-recipients';
import type { Recipient } from './compose-recipients';

function replySubject(subject: string): string {
    return subject.startsWith('Re: ') ? subject : `Re: ${subject}`;
}

function ComposeFields({
    recipients,
    replyTo,
    onSend,
    onCancel,
}: {
    recipients: Recipient[];
    replyTo: Email | null;
    onSend: (email: OutgoingEmail) => Promise<boolean>;
    onCancel: () => void;
}) {
    const { t } = useTranslation();
    const [recipient, setRecipient] = useState(
        () =>
            recipients.find(
                (entry) => entry.address === replyTo?.sender_address,
            )?.value ?? '',
    );
    const [subject, setSubject] = useState(
        replyTo ? replySubject(replyTo.subject) : '',
    );
    const [body, setBody] = useState('');
    const actions = actionsFor(useCompanySoftware());
    const [action, setAction] = useState<EmailAction>(actions[0]);
    const [isSending, setSending] = useState(false);
    const isComplete =
        recipient !== '' && subject.trim() !== '' && body.trim() !== '';

    const submit = async () => {
        setSending(true);
        const sent = await onSend({
            ...recipientFields(recipient),
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
                    value={recipient}
                    onChange={(event) => setRecipient(event.target.value)}
                >
                    <option value="">{t('inbox.choose_recipient')}</option>
                    {recipients.map((entry) => (
                        <option key={entry.value} value={entry.value}>
                            {entry.label} &lt;{entry.address}&gt;
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
    const { data: colleagues } = useApiResource<Colleague[]>(
        colleaguesIndex.url(),
    );

    return customers && colleagues ? (
        <ComposeFields
            recipients={recipientsOf(customers, colleagues)}
            {...props}
        />
    ) : (
        <AppLoading />
    );
}
