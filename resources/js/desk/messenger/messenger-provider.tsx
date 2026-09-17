import type { ReactNode } from 'react';
import { useState } from 'react';
import { index as chatMessagesIndex } from '@/routes/api/v1/chat-messages';
import { store as markReadStore } from '@/routes/api/v1/chat-messages/read';
import { store as repliesStore } from '@/routes/api/v1/chat-messages/replies';
import type { ChatMessage } from '@/types';
import { getJson, postJson } from '../api/game-api';
import { useEdition } from '../computer/edition-context';
import { sound } from '../sound/sound';
import { createDeskContext } from '../state/create-desk-context';
import { usePolledResource } from '../state/use-polled-resource';
import {
    markAllRead,
    newIncoming,
    unreadCount,
    withoutReplies,
} from './messenger-state';

const POLL_INTERVAL_MS = 10_000;
const ANSWER_REFRESH_DELAY_MS = 5_500;

type Messenger = {
    messages: ChatMessage[] | null;
    unreadCount: number;
    arrival: ChatMessage | null;
    typingContact: string | null;
    dismissArrival: () => void;
    readAll: () => void;
    reply: (message: ChatMessage, replySlug: string) => Promise<void>;
};

const { Context, useRequired } = createDeskContext<Messenger>('Messenger');

export const useMessenger = useRequired;

function fetchMessages(): Promise<ChatMessage[]> {
    return getJson<{ data: ChatMessage[] }>(chatMessagesIndex.url()).then(
        ({ data }) => data,
    );
}

export function MessengerProvider({ children }: { children: ReactNode }) {
    const edition = useEdition();
    const [arrival, setArrival] = useState<ChatMessage | null>(null);
    const [typingContact, setTypingContact] = useState<string | null>(null);
    const {
        value: messages,
        setValue: setMessages,
        refresh,
    } = usePolledResource(fetchMessages, POLL_INTERVAL_MS, {
        isEnabled: edition.apps.includes('messenger'),
        onArrival: (next, previous) => {
            const arrived = newIncoming(previous, next);

            if (arrived) {
                sound.chat();
                setArrival(arrived);
            }
        },
    });

    const readAll = () => {
        if (messages && unreadCount(messages) > 0) {
            setMessages(markAllRead(messages));
            void postJson(markReadStore.url()).catch(() => undefined);
        }
    };

    const reply = async (message: ChatMessage, replySlug: string) => {
        const { data } = await postJson<{ data: ChatMessage }>(
            repliesStore.url(message.id),
            { reply: replySlug },
        );
        setMessages(
            (current) =>
                current && [...withoutReplies(current, message.id), data],
        );
        setTypingContact(message.contact_name);
        setTimeout(
            () => void refresh().finally(() => setTypingContact(null)),
            ANSWER_REFRESH_DELAY_MS,
        );
    };

    return (
        <Context
            value={{
                messages,
                unreadCount: unreadCount(messages ?? []),
                arrival,
                typingContact,
                dismissArrival: () => setArrival(null),
                readAll,
                reply,
            }}
        >
            {children}
        </Context>
    );
}
