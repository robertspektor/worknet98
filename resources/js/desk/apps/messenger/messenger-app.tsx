import { useEffect, useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { useMessenger } from '../../messenger/messenger-provider';
import { contactsOf, conversationWith } from '../../messenger/messenger-state';
import { AppLoading } from '../../ui/app-loading';
import { PixelIcon } from '../../ui/pixel-icon';
import { ChatLog } from './chat-log';
import { ReplyBar } from './reply-bar';

export function MessengerApp() {
    const { t } = useTranslation();
    const messenger = useMessenger();
    const [selectedContact, setSelectedContact] = useState<string | null>(null);

    useEffect(() => messenger.readAll(), [messenger.messages]);

    if (!messenger.messages) {
        return <AppLoading />;
    }

    const contacts = contactsOf(messenger.messages);
    const contact = selectedContact ?? contacts[0] ?? null;
    const conversation = contact
        ? conversationWith(messenger.messages, contact)
        : [];

    return (
        <div className="messenger-app">
            <div className="messenger-contacts sunken" role="listbox">
                <div className="messenger-contacts-header">
                    {t('messenger.contacts')}
                </div>
                {contacts.map((name) => (
                    <button
                        key={name}
                        type="button"
                        role="option"
                        aria-selected={name === contact}
                        className={`messenger-contact ${name === contact ? 'is-selected' : ''}`}
                        onClick={() => setSelectedContact(name)}
                    >
                        <PixelIcon name="messenger" size={16} />
                        <span>{name}</span>
                    </button>
                ))}
            </div>
            <div className="messenger-conversation">
                {contact ? (
                    <ChatLog
                        messages={conversation}
                        isTyping={messenger.typingContact === contact}
                        contact={contact}
                    />
                ) : (
                    <p className="messenger-empty sunken">
                        {t('messenger.empty')}
                    </p>
                )}
                <ReplyBar message={conversation.at(-1) ?? null} />
            </div>
        </div>
    );
}
