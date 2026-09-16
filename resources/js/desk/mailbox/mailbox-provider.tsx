import type { ReactNode } from 'react';
import { createContext, use, useEffect, useState } from 'react';
import { index as emailsIndex } from '@/routes/api/v1/emails';
import { store as markReadStore } from '@/routes/api/v1/emails/read';
import type { Email } from '@/types';
import { getJson, postJson } from '../api/game-api';
import { sound } from '../sound/sound';
import { hasNewUnread, markRead, unreadCount } from './mailbox-state';

const POLL_INTERVAL_MS = 15_000;

type Mailbox = {
    emails: Email[] | null;
    unreadCount: number;
    read: (id: number) => void;
};

const MailboxContext = createContext<Mailbox | null>(null);

function fetchEmails(): Promise<Email[]> {
    return getJson<{ data: Email[] }>(emailsIndex.url()).then(
        ({ data }) => data,
    );
}

export function MailboxProvider({ children }: { children: ReactNode }) {
    const [emails, setEmails] = useState<Email[] | null>(null);

    useEffect(() => {
        let previous: Email[] | null = null;

        const refresh = () =>
            fetchEmails()
                .then((next) => {
                    if (previous && hasNewUnread(previous, next)) {
                        sound.mail();
                    }
                    previous = next;
                    setEmails(next);
                })
                .catch(() => undefined);

        void refresh();
        const timer = setInterval(() => void refresh(), POLL_INTERVAL_MS);

        return () => clearInterval(timer);
    }, []);

    const read = (id: number) => {
        setEmails((current) => current && markRead(current, id));
        void postJson(markReadStore.url(id)).catch(() => undefined);
    };

    return (
        <MailboxContext
            value={{
                emails,
                unreadCount: unreadCount(emails ?? []),
                read,
            }}
        >
            {children}
        </MailboxContext>
    );
}

export function useMailbox(): Mailbox {
    const mailbox = use(MailboxContext);

    if (!mailbox) {
        throw new Error('useMailbox must be used inside MailboxProvider.');
    }

    return mailbox;
}
