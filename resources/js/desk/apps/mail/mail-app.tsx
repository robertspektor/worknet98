import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import type { Email, EmailFolder } from '@/types';
import { useMailbox } from '../../mailbox/mailbox-provider';
import type { OutgoingEmail } from '../../mailbox/mailbox-provider';
import { sound } from '../../sound/sound';
import { AppLoading } from '../../ui/app-loading';
import { useRefusalAlert } from '../../ui/use-refusal-alert';
import { ComposeForm } from './compose-form';
import { EmailList } from './email-list';
import { EmailReader } from './email-reader';

const FOLDERS: EmailFolder[] = ['inbox', 'sent'];

type Compose = { replyTo: Email | null } | null;

export function MailApp() {
    const { t } = useTranslation();
    const mailbox = useMailbox();
    const alertRefusal = useRefusalAlert();
    const [folder, setFolder] = useState<EmailFolder>('inbox');
    const [selectedId, setSelectedId] = useState<number | null>(null);
    const [compose, setCompose] = useState<Compose>(null);
    const canWrite = mailbox.scope === 'work';

    if (!mailbox.emails) {
        return <AppLoading />;
    }

    const emails = mailbox.emails.filter((email) => email.folder === folder);
    const selected = emails.find((email) => email.id === selectedId) ?? null;

    const select = (email: Email) => {
        setSelectedId(email.id);

        if (!email.is_read) {
            mailbox.read(email.id);
        }
    };

    const send = async (outgoing: OutgoingEmail) => {
        try {
            const sent = await mailbox.send(outgoing);
            sound.click();
            setFolder('sent');
            setSelectedId(sent.id);

            return true;
        } catch (error) {
            alertRefusal(t('desktop.mail'), error);

            return false;
        }
    };

    return (
        <div className="mail-app">
            {canWrite && (
                <div className="mail-toolbar">
                    <button
                        type="button"
                        className="button"
                        onClick={() => setCompose({ replyTo: null })}
                    >
                        {t('inbox.new_message')}
                    </button>
                    <button
                        type="button"
                        className="button"
                        disabled={
                            !selected || selected.folder === 'sent' || !!compose
                        }
                        onClick={() => setCompose({ replyTo: selected })}
                    >
                        {t('inbox.reply')}
                    </button>
                    <span className="mail-toolbar-separator" />
                    {FOLDERS.map((entry) => (
                        <button
                            key={entry}
                            type="button"
                            className={`button ${folder === entry && !compose ? 'is-pressed' : ''}`}
                            onClick={() => {
                                setFolder(entry);
                                setCompose(null);
                            }}
                        >
                            {t(`inbox.folders.${entry}`)}
                        </button>
                    ))}
                </div>
            )}
            {compose ? (
                <ComposeForm
                    replyTo={compose.replyTo}
                    onSend={send}
                    onCancel={() => setCompose(null)}
                />
            ) : (
                <div className="mail-panes">
                    <EmailList
                        emails={emails}
                        folder={folder}
                        selectedId={selectedId}
                        onSelect={select}
                    />
                    <EmailReader email={selected} />
                </div>
            )}
        </div>
    );
}
