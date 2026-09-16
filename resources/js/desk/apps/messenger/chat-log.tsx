import { useEffect, useRef } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import type { ChatMessage } from '@/types';
import { formatTime } from '../../ui/format';

export function ChatLog({
    messages,
    contact,
    isTyping,
}: {
    messages: ChatMessage[];
    contact: string;
    isTyping: boolean;
}) {
    const { t, locale } = useTranslation();
    const end = useRef<HTMLDivElement>(null);

    useEffect(() => {
        end.current?.scrollIntoView({ block: 'end' });
    }, [messages.length, isTyping]);

    return (
        <div className="chat-log sunken">
            {messages.map((message) => (
                <div key={message.id} className="chat-entry">
                    <div
                        className={`chat-author ${message.is_from_player ? 'is-player' : ''}`}
                    >
                        {message.is_from_player
                            ? t('messenger.you_say')
                            : t('messenger.says', {
                                  name: message.contact_name,
                              })}
                        <span className="chat-time">
                            {formatTime(message.sent_at, locale)}
                        </span>
                    </div>
                    <p className="chat-text">{message.body}</p>
                </div>
            ))}
            {isTyping && (
                <p className="chat-typing">
                    {t('messenger.typing', { name: contact })}
                </p>
            )}
            <div ref={end} />
        </div>
    );
}
