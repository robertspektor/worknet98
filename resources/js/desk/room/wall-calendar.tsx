import { formatGameMonth } from '../clock/game-clock';
import { monthDays, weekdayHeads } from '../clock/month-days';
import { useGameTime } from '../clock/use-game-time';
import { useTranslation } from '@/i18n/use-translation';

/* The spiral calendar on the wall. It shows the month the game is in, with
   the weekdays in their columns and the day the player is on circled. */

export function WallCalendar() {
    const { locale } = useTranslation();
    const gameTime = useGameTime();

    return (
        <div className="wall-calendar" data-focus="calendar" aria-hidden="true">
            <span className="calendar-hook" />
            <span className="calendar-ring" />
            <span className="calendar-spiral" />
            <span className="calendar-sheet">
                <span className="calendar-month">
                    {formatGameMonth(gameTime, locale)}
                </span>
                <span className="calendar-weekdays">
                    {weekdayHeads(locale).map((head) => (
                        <span key={head} className="calendar-weekday">
                            {head}
                        </span>
                    ))}
                </span>
                <span className="calendar-grid">
                    {monthDays(gameTime).map((cell, index) => (
                        <span
                            key={index}
                            className={`calendar-day${cell.isToday ? ' is-today' : ''}`}
                        >
                            {cell.day}
                        </span>
                    ))}
                </span>
            </span>
        </div>
    );
}
