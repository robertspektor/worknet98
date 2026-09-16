import { useTranslation } from '@/i18n/use-translation';
import {
    destroy as entriesDestroy,
    index as entriesIndex,
    store as entriesStore,
} from '@/routes/api/v1/calendar-entries';
import type { CalendarEntry } from '@/types';
import { deleteJson, postJson } from '../../api/game-api';
import { useApiResource } from '../../api/use-api-resource';
import { AppLoading } from '../../ui/app-loading';
import { formatDay } from '../../ui/format';
import { PixelGlyph } from '../../ui/pixel-icon';
import { useRefusalAlert } from '../../ui/use-refusal-alert';
import { CalendarEntryForm } from './calendar-entry-form';
import type { NewCalendarEntry } from './calendar-entry-form';

export function CalendarApp() {
    const { t, locale } = useTranslation();
    const alertRefusal = useRefusalAlert();
    const entries = useApiResource<CalendarEntry[]>(entriesIndex.url());

    if (!entries.data) {
        return <AppLoading />;
    }

    const add = async (entry: NewCalendarEntry) => {
        try {
            await postJson(entriesStore.url(), entry);
            entries.reload();

            return true;
        } catch (error) {
            alertRefusal(t('desktop.calendar'), error);

            return false;
        }
    };

    const remove = async (id: number) => {
        await deleteJson(entriesDestroy.url(id)).catch((error: unknown) =>
            alertRefusal(t('desktop.calendar'), error),
        );
        entries.reload();
    };

    return (
        <div className="calendar-app">
            <CalendarEntryForm onAdd={add} />
            <ul className="calendar-list sunken">
                {entries.data.length === 0 && (
                    <li className="muted calendar-empty">
                        {t('calendar.empty')}
                    </li>
                )}
                {entries.data.map((entry) => (
                    <li key={entry.id} className="calendar-entry">
                        <span className="calendar-when">
                            {formatDay(entry.date, locale)} {entry.time}
                        </span>
                        <span className="calendar-title">{entry.title}</span>
                        <button
                            type="button"
                            className="title-button"
                            aria-label={t('calendar.remove')}
                            onClick={() => void remove(entry.id)}
                        >
                            <PixelGlyph name="close" />
                        </button>
                    </li>
                ))}
            </ul>
        </div>
    );
}
