import type { ReactNode } from 'react';
import {
    index as emailsIndex,
    store as emailsStore,
} from '@/routes/api/v1/emails';
import { store as markReadStore } from '@/routes/api/v1/emails/read';
import type { Email, EmailAction, MailboxScope } from '@/types';
import { getJson, postJson } from '../api/game-api';
import { useEdition } from '../computer/edition-context';
import { sound } from '../sound/sound';
import { createDeskContext } from '../state/create-desk-context';
import { usePolledResource } from '../state/use-polled-resource';
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

const { Context, useRequired } = createDeskContext<Mailbox>('Mailbox');

export const useMailbox = useRequired;

function fetchEmails(scope: MailboxScope): Promise<Email[]> {
    return getJson<{ data: Email[] }>(
        emailsIndex.url({ query: { mailbox: scope } }),
    ).then(({ data }) => data);
}

export function MailboxProvider({ children }: { children: ReactNode }) {
    const { mailbox: scope } = useEdition();
    const { value: emails, setValue: setEmails } = usePolledResource(
        () => fetchEmails(scope),
        POLL_INTERVAL_MS,
        {
            restartKey: scope,
            onArrival: (next, previous) => {
                if (hasNewUnread(previous, next)) {
                    sound.mail();
                }
            },
        },
    );

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
        <Context
            value={{
                scope,
                emails,
                unreadCount: unreadCount(emails ?? []),
                read,
                send,
            }}
        >
            {children}
        </Context>
    );
}
