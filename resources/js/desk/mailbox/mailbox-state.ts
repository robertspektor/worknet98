import type { Email } from '@/types';

export function unreadCount(emails: Email[]): number {
    return emails.filter((email) => !email.is_read).length;
}

export function hasNewUnread(previous: Email[], next: Email[]): boolean {
    const knownIds = new Set(previous.map((email) => email.id));

    return next.some((email) => !email.is_read && !knownIds.has(email.id));
}

export function markRead(emails: Email[], id: number): Email[] {
    return emails.map((email) =>
        email.id === id ? { ...email, is_read: true } : email,
    );
}
