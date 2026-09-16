import type { ChatMessage } from '@/types';

export function unreadCount(messages: ChatMessage[]): number {
    return messages.filter((message) => !message.is_read).length;
}

export function newIncoming(
    previous: ChatMessage[],
    next: ChatMessage[],
): ChatMessage | null {
    const knownIds = new Set(previous.map((message) => message.id));
    const arrived = next.filter(
        (message) => !message.is_from_player && !knownIds.has(message.id),
    );

    return arrived.at(-1) ?? null;
}

export function contactsOf(messages: ChatMessage[]): string[] {
    return [
        ...new Set(
            messages.toReversed().map((message) => message.contact_name),
        ),
    ];
}

export function conversationWith(
    messages: ChatMessage[],
    contact: string,
): ChatMessage[] {
    return messages.filter((message) => message.contact_name === contact);
}

export function markAllRead(messages: ChatMessage[]): ChatMessage[] {
    return messages.map((message) =>
        message.is_read ? message : { ...message, is_read: true },
    );
}

export function withoutReplies(
    messages: ChatMessage[],
    id: number,
): ChatMessage[] {
    return messages.map((message) =>
        message.id === id ? { ...message, replies: [] } : message,
    );
}
