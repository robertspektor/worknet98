import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import type { ChatMessage } from '@/types';
import { useMessenger } from '../../messenger/messenger-provider';
import { sound } from '../../sound/sound';
import { useRefusalAlert } from '../../ui/use-refusal-alert';

export function ReplyBar({ message }: { message: ChatMessage | null }) {
    const { t } = useTranslation();
    const messenger = useMessenger();
    const alertRefusal = useRefusalAlert();
    const [isSending, setSending] = useState(false);

    if (!message || message.replies.length === 0) {
        return null;
    }

    const send = async (replySlug: string) => {
        setSending(true);

        try {
            await messenger.reply(message, replySlug);
            sound.click();
        } catch (error) {
            alertRefusal(t('desktop.messenger'), error);
        } finally {
            setSending(false);
        }
    };

    return (
        <div className="reply-bar">
            <span className="reply-bar-label">{t('messenger.reply_with')}</span>
            <div className="reply-options">
                {message.replies.map((reply) => (
                    <button
                        key={reply.slug}
                        type="button"
                        className="button reply-button"
                        disabled={isSending}
                        onClick={() => void send(reply.slug)}
                    >
                        {reply.text}
                    </button>
                ))}
            </div>
        </div>
    );
}
