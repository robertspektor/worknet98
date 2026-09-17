import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { index as threadsIndex } from '@/routes/api/v1/forum/threads';
import type { ForumThread } from '@/types';
import { useApiResource } from '../../api/use-api-resource';
import { useCompanySoftware } from '../../company-software/company-software-provider';
import { AppLoading } from '../../ui/app-loading';
import { NewThreadForm } from './new-thread-form';
import { ThreadList } from './thread-list';
import { RankingsTab } from './rankings-tab';
import { ThreadView } from './thread-view';

export function ForumApp() {
    const { t } = useTranslation();
    const software = useCompanySoftware();
    const threads = useApiResource<ForumThread[]>(threadsIndex.url());
    const [openThread, setOpenThread] = useState<ForumThread | null>(null);
    const [isWriting, setWriting] = useState(false);
    const [tab, setTab] = useState<'threads' | 'rankings'>('threads');

    if (!threads.data) {
        return <AppLoading />;
    }

    const close = () => {
        setWriting(false);
        setOpenThread(null);
        threads.reload();
    };

    return (
        <div className="company-app forum-app">
            <header className="company-app-bar">
                <span className="company-app-name">{t('forum.title')}</span>
                <span className="company-app-company">{software?.company}</span>
            </header>
            <div className="day-tabs" role="tablist">
                {(['threads', 'rankings'] as const).map((entry) => (
                    <button
                        key={entry}
                        type="button"
                        role="tab"
                        aria-selected={entry === tab}
                        className={`day-tab ${entry === tab ? 'is-active' : ''}`}
                        onClick={() => setTab(entry)}
                    >
                        {t(`forum.tabs.${entry}`)}
                    </button>
                ))}
            </div>
            {tab === 'rankings' ? (
                <RankingsTab />
            ) : isWriting ? (
                <NewThreadForm
                    onDone={close}
                    onCancel={() => setWriting(false)}
                />
            ) : openThread ? (
                <ThreadView thread={openThread} onBack={close} />
            ) : (
                <ThreadList
                    threads={threads.data}
                    onOpen={setOpenThread}
                    onWrite={() => setWriting(true)}
                />
            )}
        </div>
    );
}
