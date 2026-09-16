import { describe, expect, it } from 'vitest';
import type { ChatMessage } from '@/types';
import {
    contactsOf,
    conversationWith,
    markAllRead,
    newIncoming,
    unreadCount,
    withoutReplies,
} from './messenger-state';

function message(overrides: Partial<ChatMessage>): ChatMessage {
    return {
        id: 1,
        contact_name: 'Bev Mercer',
        is_from_player: false,
        body: 'psst.',
        sent_at: '2026-09-21T09:00:00+00:00',
        is_read: false,
        replies: [],
        ...overrides,
    };
}

describe('messenger state', () => {
    it('counts unread messages', () => {
        expect(
            unreadCount([
                message({ id: 1 }),
                message({ id: 2, is_read: true }),
            ]),
        ).toBe(1);
    });

    it('finds the latest newly arrived incoming message', () => {
        const previous = [message({ id: 1 })];
        const next = [
            message({ id: 1 }),
            message({ id: 2, is_from_player: true }),
            message({ id: 3, body: 'first' }),
            message({ id: 4, body: 'latest' }),
        ];

        expect(newIncoming(previous, next)?.body).toBe('latest');
    });

    it('ignores messages the player sent', () => {
        expect(
            newIncoming([], [message({ id: 2, is_from_player: true })]),
        ).toBeNull();
    });

    it('lists contacts with the most recent conversation first', () => {
        const messages = [
            message({ id: 1, contact_name: 'Bev Mercer' }),
            message({ id: 2, contact_name: 'Gary Flowright' }),
            message({ id: 3, contact_name: 'Bev Mercer' }),
        ];

        expect(contactsOf(messages)).toEqual(['Bev Mercer', 'Gary Flowright']);
    });

    it('filters the conversation with one contact', () => {
        const messages = [
            message({ id: 1, contact_name: 'Bev Mercer' }),
            message({ id: 2, contact_name: 'Gary Flowright' }),
        ];

        expect(
            conversationWith(messages, 'Gary Flowright').map(({ id }) => id),
        ).toEqual([2]);
    });

    it('marks all messages as read', () => {
        expect(unreadCount(markAllRead([message({ id: 1 })]))).toBe(0);
    });

    it('removes the replies of an answered message', () => {
        const messages = [
            message({ id: 1, replies: [{ slug: 'thanks', text: 'Thanks' }] }),
        ];

        expect(withoutReplies(messages, 1)[0].replies).toEqual([]);
    });
});
