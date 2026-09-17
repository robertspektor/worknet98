import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { store as threadsStore } from '@/routes/api/v1/forum/threads';
import { postJson } from '../../api/game-api';
import { useRefusalAlert } from '../../ui/use-refusal-alert';

export function NewThreadForm({
    onDone,
    onCancel,
}: {
    onDone: () => void;
    onCancel: () => void;
}) {
    const { t } = useTranslation();
    const alertRefusal = useRefusalAlert();
    const [title, setTitle] = useState('');
    const [body, setBody] = useState('');
    const [isBusy, setBusy] = useState(false);

    const submit = async () => {
        setBusy(true);

        try {
            await postJson(threadsStore.url(), { title, body });
            onDone();
        } catch (error) {
            alertRefusal(t('forum.title'), error);
        } finally {
            setBusy(false);
        }
    };

    return (
        <form
            className="forum-new"
            onSubmit={(event) => {
                event.preventDefault();
                void submit();
            }}
        >
            <label className="compose-field" htmlFor="forum-title">
                <span>{t('forum.subject')}:</span>
                <input
                    id="forum-title"
                    className="input"
                    maxLength={120}
                    value={title}
                    onChange={(event) => setTitle(event.target.value)}
                />
            </label>
            <textarea
                className="input forum-new-body"
                aria-label={t('forum.message')}
                maxLength={2000}
                value={body}
                onChange={(event) => setBody(event.target.value)}
            />
            <div className="app-actions app-actions-end">
                <button type="button" className="button" onClick={onCancel}>
                    {t('dialog.cancel')}
                </button>
                <button
                    type="submit"
                    className="button button-primary"
                    disabled={
                        isBusy ||
                        title.trim().length < 3 ||
                        body.trim().length < 3
                    }
                >
                    {t('forum.post')}
                </button>
            </div>
        </form>
    );
}
