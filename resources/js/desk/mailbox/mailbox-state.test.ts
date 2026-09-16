import { describe, expect, it } from 'vite-plus/test';
import type { Email } from '@/types';
import { hasNewUnread, markRead, unreadCount } from './mailbox-state';

function email(id: number, isRead = false): Email {
    return {
        id,
        sender_name: 'Brenda Kowalczyk',
        sender_address: 'brenda@transglobal.wn',
        subject: 'Hello',
        body: 'Welcome.',
        received_at: '2026-09-17T09:00:00+00:00',
        is_read: isRead,
        folder: 'inbox',
        recipient_name: null,
        recipient_address: null,
        action: null,
    };
}

describe('mailbox state', () => {
    it('counts unread emails', () => {
        expect(unreadCount([email(1), email(2, true), email(3)])).toBe(2);
    });

    it('detects unread emails that were not there before', () => {
        expect(hasNewUnread([email(1)], [email(2), email(1)])).toBe(true);
    });

    it('ignores known and already read emails', () => {
        expect(hasNewUnread([email(1)], [email(1), email(2, true)])).toBe(
            false,
        );
    });

    it('marks a single email as read', () => {
        expect(markRead([email(1), email(2)], 2)).toEqual([
            email(1),
            email(2, true),
        ]);
    });
});
