import { useState } from 'react';
import type { Email } from '@/types';
import { useMailbox } from '../../mailbox/mailbox-provider';
import { AppLoading } from '../../ui/app-loading';
import { EmailList } from './email-list';
import { EmailReader } from './email-reader';

export function MailApp() {
    const mailbox = useMailbox();
    const [selectedId, setSelectedId] = useState<number | null>(null);

    if (!mailbox.emails) {
        return <AppLoading />;
    }

    const select = (email: Email) => {
        setSelectedId(email.id);

        if (!email.is_read) {
            mailbox.read(email.id);
        }
    };

    return (
        <div className="mail-app">
            <EmailList
                emails={mailbox.emails}
                selectedId={selectedId}
                onSelect={select}
            />
            <EmailReader
                email={
                    mailbox.emails.find((email) => email.id === selectedId) ??
                    null
                }
            />
        </div>
    );
}
