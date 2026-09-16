import { useEffect, useRef, useState } from 'react';
import { show, update } from '@/routes/api/v1/note';
import { getJson, putJson } from '../../api/game-api';

const SAVE_DELAY_MS = 800;

export type NoteState = 'loading' | 'saving' | 'saved';

export function useNote() {
    const [body, setBody] = useState('');
    const [state, setState] = useState<NoteState>('loading');
    const saveTimer = useRef<ReturnType<typeof setTimeout>>(undefined);

    useEffect(() => {
        void getJson<{ data: { body: string } }>(show.url())
            .then(({ data }) => setBody(data.body))
            .finally(() => setState('saved'));

        return () => clearTimeout(saveTimer.current);
    }, []);

    const write = (next: string) => {
        setBody(next);
        setState('saving');
        clearTimeout(saveTimer.current);
        saveTimer.current = setTimeout(
            () =>
                void putJson(update.url(), { body: next })
                    .then(() => setState('saved'))
                    .catch(() => undefined),
            SAVE_DELAY_MS,
        );
    };

    return { body, state, write };
}
