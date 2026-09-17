import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { gameToday } from '../../clock/game-clock';
import { useGameClock } from '../../clock/use-game-clock';
import { formatDay } from '../../ui/format';
import { timeOptions, upcomingDates } from './calendar-options';

const DATE_COUNT = 14;

export type NewCalendarEntry = { date: string; time: string; title: string };

export function CalendarEntryForm({
    onAdd,
}: {
    onAdd: (entry: NewCalendarEntry) => Promise<boolean>;
}) {
    const { t, locale } = useTranslation();
    const settings = useGameClock();
    const [dates] = useState(() =>
        upcomingDates(gameToday(settings, Date.now()), DATE_COUNT),
    );
    const [date, setDate] = useState(dates[0]);
    const [time, setTime] = useState('10:00');
    const [title, setTitle] = useState('');
    const [isBusy, setBusy] = useState(false);

    const submit = async () => {
        setBusy(true);
        const added = await onAdd({ date, time, title }).finally(() =>
            setBusy(false),
        );

        if (added) {
            setTitle('');
        }
    };

    return (
        <form
            className="calendar-form"
            onSubmit={(event) => {
                event.preventDefault();
                void submit();
            }}
        >
            <select
                className="select"
                aria-label={t('calendar.date')}
                value={date}
                onChange={(event) => setDate(event.target.value)}
            >
                {dates.map((entry) => (
                    <option key={entry} value={entry}>
                        {formatDay(entry, locale)}
                    </option>
                ))}
            </select>
            <select
                className="select"
                aria-label={t('calendar.time')}
                value={time}
                onChange={(event) => setTime(event.target.value)}
            >
                {timeOptions().map((option) => (
                    <option key={option} value={option}>
                        {option}
                    </option>
                ))}
            </select>
            <input
                className="input"
                aria-label={t('calendar.title_label')}
                placeholder={t('calendar.title_placeholder')}
                maxLength={80}
                value={title}
                onChange={(event) => setTitle(event.target.value)}
            />
            <button
                type="submit"
                className="button button-primary"
                disabled={isBusy || title.trim() === ''}
            >
                {t('calendar.add')}
            </button>
        </form>
    );
}
