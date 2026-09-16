import type { ReactNode } from 'react';
import { createContext, use, useEffect, useState } from 'react';
import {
    index as emailsIndex,
    store as emailsStore,
} from '@/routes/api/v1/emails';
import { store as markReadStore } from '@/routes/api/v1/emails/read';
import type { Email, EmailAction, MailboxScope } from '@/types';
import { getJson, postJson } from '../api/game-api';
import { useEdition } from '../computer/edition-context';
import { sound } from '../sound/sound';
import { hasNewUnread, markRead, unreadCount } from './mailbox-state';

const POLL_INTERVAL_MS = 15_000;

export type OutgoingEmail = {
    customer_id: number;
    subject: string;
    body: string;
    action: EmailAction;
};

type Mailbox = {
    scope: MailboxScope;
    emails: Email[] | null;
    unreadCount: number;
    read: (id: number) => void;
    send: (email: OutgoingEmail) => Promise<Email>;
};

const MailboxContext = createContext<Mailbox | null>(null);

function fetchEmails(scope: MailboxScope): Promise<Email[]> {
    return getJson<{ data: Email[] }>(
        emailsIndex.url({ query: { mailbox: scope } }),
    ).then(({ data }) => data);
}

export function MailboxProvider({ children }: { children: ReactNode }) {
    const { mailbox: scope } = useEdition();
    const [emails, setEmails] = useState<Email[] | null>(null);

    useEffect(() => {
        let previous: Email[] | null = null;

        const refresh = () =>
            fetchEmails(scope)
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
    }, [scope]);

    const read = (id: number) => {
        setEmails((current) => current && markRead(current, id));
        void postJson(markReadStore.url(id)).catch(() => undefined);
    };

    const send = async (outgoing: OutgoingEmail) => {
        const { data } = await postJson<{ data: Email }>(
            emailsStore.url(),
            outgoing,
        );
        setEmails((current) => current && [data, ...current]);

        return data;
    };

    return (
        <MailboxContext
            value={{
                scope,
                emails,
                unreadCount: unreadCount(emails ?? []),
                read,
                send,
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
