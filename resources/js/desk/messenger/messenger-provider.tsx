import type { ReactNode } from 'react';
import { createContext, use, useEffect, useRef, useState } from 'react';
import { index as chatMessagesIndex } from '@/routes/api/v1/chat-messages';
import { store as markReadStore } from '@/routes/api/v1/chat-messages/read';
import { store as repliesStore } from '@/routes/api/v1/chat-messages/replies';
import type { ChatMessage } from '@/types';
import { getJson, postJson } from '../api/game-api';
import { useEdition } from '../computer/edition-context';
import { sound } from '../sound/sound';
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

const MessengerContext = createContext<Messenger | null>(null);

function fetchMessages(): Promise<ChatMessage[]> {
    return getJson<{ data: ChatMessage[] }>(chatMessagesIndex.url()).then(
        ({ data }) => data,
    );
}

function usePolledMessages(isEnabled: boolean) {
    const [messages, setMessages] = useState<ChatMessage[] | null>(null);
    const [arrival, setArrival] = useState<ChatMessage | null>(null);
    const previous = useRef<ChatMessage[] | null>(null);

    const refresh = () =>
        fetchMessages()
            .then((next) => {
                const arrived =
                    previous.current && newIncoming(previous.current, next);

                if (arrived) {
                    sound.chat();
                    setArrival(arrived);
                }
                previous.current = next;
                setMessages(next);
            })
            .catch(() => undefined);

    useEffect(() => {
        if (!isEnabled) {
            return;
        }

        void refresh();
        const timer = setInterval(() => void refresh(), POLL_INTERVAL_MS);

        return () => clearInterval(timer);
    }, [isEnabled]);

    return { messages, setMessages, arrival, setArrival, refresh };
}

export function MessengerProvider({ children }: { children: ReactNode }) {
    const edition = useEdition();
    const { messages, setMessages, arrival, setArrival, refresh } =
        usePolledMessages(edition.apps.includes('messenger'));
    const [typingContact, setTypingContact] = useState<string | null>(null);

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
        <MessengerContext
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
        </MessengerContext>
    );
}

export function useMessenger(): Messenger {
    const messenger = use(MessengerContext);

    if (!messenger) {
        throw new Error('useMessenger must be used inside MessengerProvider.');
    }

    return messenger;
}
