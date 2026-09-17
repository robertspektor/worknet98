import { useTranslation } from '@/i18n/use-translation';
import type { ForumThread } from '@/types';
import { formatDateTime } from '../../ui/format';

export function ThreadList({
    threads,
    onOpen,
    onWrite,
}: {
    threads: ForumThread[];
    onOpen: (thread: ForumThread) => void;
    onWrite: () => void;
}) {
    const { t, locale } = useTranslation();

    return (
        <>
            <div className="forum-list sunken">
                {threads.length === 0 && (
                    <p className="muted records-empty">{t('forum.empty')}</p>
                )}
                {threads.map((thread) => (
                    <button
                        key={thread.id}
                        type="button"
                        className="forum-row"
                        onClick={() => onOpen(thread)}
                    >
                        <span className="forum-row-title">{thread.title}</span>
                        <span className="forum-row-meta">
                            {t('forum.posts', {
                                count: thread.posts_count,
                            })}{' '}
                            &middot; {thread.author} &middot;{' '}
                            {formatDateTime(thread.last_posted_at, locale)}
                        </span>
                    </button>
                ))}
            </div>
            <div className="app-actions app-actions-end">
                <button
                    type="button"
                    className="button button-primary"
                    onClick={onWrite}
                >
                    {t('forum.new_thread')}
                </button>
            </div>
        </>
    );
}
