import { useEffect, useRef, useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { destroy as postDestroy } from '@/routes/api/v1/forum/posts';
import {
    index as postsIndex,
    store as postsStore,
} from '@/routes/api/v1/forum/threads/posts';
import type { ForumPost, ForumThread } from '@/types';
import { deleteJson, postJson } from '../../api/game-api';
import { useApiResource } from '../../api/use-api-resource';
import { AppLoading } from '../../ui/app-loading';
import { formatDateTime } from '../../ui/format';
import { useRefusalAlert } from '../../ui/use-refusal-alert';

export function ThreadView({
    thread,
    onBack,
}: {
    thread: ForumThread;
    onBack: () => void;
}) {
    const { t, locale } = useTranslation();
    const alertRefusal = useRefusalAlert();
    const posts = useApiResource<ForumPost[]>(postsIndex.url(thread.id));
    const [body, setBody] = useState('');
    const [isBusy, setBusy] = useState(false);
    const postsRef = useRef<HTMLDivElement>(null);
    const postCount = posts.data?.length ?? 0;

    useEffect(() => {
        const list = postsRef.current;

        if (list) {
            list.scrollTop = list.scrollHeight;
        }
    }, [postCount]);

    if (!posts.data) {
        return <AppLoading />;
    }

    const run = async (action: () => Promise<unknown>) => {
        setBusy(true);

        try {
            await action();
            posts.reload();
        } catch (error) {
            alertRefusal(t('forum.title'), error);
        } finally {
            setBusy(false);
        }
    };

    const reply = () =>
        run(async () => {
            await postJson(postsStore.url(thread.id), { body });
            setBody('');
        });

    return (
        <>
            <h3 className="forum-thread-title">{thread.title}</h3>
            <div ref={postsRef} className="forum-posts sunken">
                {posts.data.map((post) => (
                    <article key={post.id} className="forum-post">
                        <header className="forum-post-head">
                            <b>{post.author}</b> &middot; {post.author_title}{' '}
                            &middot; {formatDateTime(post.posted_at, locale)}
                            {post.is_own && (
                                <button
                                    type="button"
                                    className="forum-delete"
                                    disabled={isBusy}
                                    onClick={() =>
                                        void run(() =>
                                            deleteJson(
                                                postDestroy.url(post.id),
                                            ),
                                        )
                                    }
                                >
                                    {t('forum.delete')}
                                </button>
                            )}
                        </header>
                        <p className="forum-post-body">{post.body}</p>
                    </article>
                ))}
            </div>
            <form
                className="forum-reply"
                onSubmit={(event) => {
                    event.preventDefault();
                    void reply();
                }}
            >
                <textarea
                    className="input forum-reply-body"
                    aria-label={t('forum.reply')}
                    maxLength={2000}
                    value={body}
                    onChange={(event) => setBody(event.target.value)}
                />
                <div className="app-actions app-actions-end">
                    <button type="button" className="button" onClick={onBack}>
                        {t('forum.back')}
                    </button>
                    <button
                        type="submit"
                        className="button button-primary"
                        disabled={isBusy || body.trim().length < 3}
                    >
                        {t('forum.reply')}
                    </button>
                </div>
            </form>
        </>
    );
}
