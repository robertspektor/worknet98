import { useTranslation } from '@/i18n/use-translation';
import { AppLoading } from '../../ui/app-loading';
import { useNote } from './use-note';

export function NotepadApp() {
    const { t } = useTranslation();
    const note = useNote();

    if (note.state === 'loading') {
        return <AppLoading />;
    }

    return (
        <div className="notepad">
            <textarea
                className="notepad-text sunken"
                value={note.body}
                maxLength={10000}
                placeholder={t('notepad.placeholder')}
                spellCheck={false}
                onChange={(event) => note.write(event.target.value)}
            />
            <div className="notepad-status">
                {t(
                    note.state === 'saving'
                        ? 'notepad.saving'
                        : 'notepad.saved',
                )}
            </div>
        </div>
    );
}
