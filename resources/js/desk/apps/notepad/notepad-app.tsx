import { useTranslation } from '@/i18n/use-translation';
import { AppLoading } from '../../ui/app-loading';
import { useNote } from './use-note';
import { useSaveToDisk } from './use-save-to-disk';

export function NotepadApp() {
    const { t } = useTranslation();
    const note = useNote();
    const disk = useSaveToDisk();

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
                {disk.canSave && (
                    <button
                        type="button"
                        className="button"
                        onClick={() => void disk.save(note.body)}
                    >
                        {t('notepad.save_to_disk')}
                    </button>
                )}
                <span className="notepad-save-state">
                    {t(
                        note.state === 'saving'
                            ? 'notepad.saving'
                            : 'notepad.saved',
                    )}
                </span>
            </div>
        </div>
    );
}
