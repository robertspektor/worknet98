const WEEK_LENGTH = 7;
const WEEK_ROWS = 6;
const A_MONDAY = Date.UTC(1998, 0, 5);

export type MonthDay = { day: number | null; isToday: boolean };

/* Game dates are read in UTC, so the calendar agrees with the clock. */

function mondayIndex(date: Date): number {
    return (date.getUTCDay() + 6) % WEEK_LENGTH;
}

function lastDayOf(year: number, month: number): number {
    return new Date(Date.UTC(year, month + 1, 0)).getUTCDate();
}

/* The month the game is in, laid out as a calendar sheet: six weeks of seven
   cells, the days of the month in their weekday column, everything before
   the first and after the last left empty. */

export function monthDays(gameTime: Date): MonthDay[] {
    const year = gameTime.getUTCFullYear();
    const month = gameTime.getUTCMonth();
    const lead = mondayIndex(new Date(Date.UTC(year, month, 1)));
    const length = lastDayOf(year, month);
    const today = gameTime.getUTCDate();

    return Array.from({ length: WEEK_ROWS * WEEK_LENGTH }, (_, cell) => {
        const day = cell - lead + 1;

        return day >= 1 && day <= length
            ? { day, isToday: day === today }
            : { day: null, isToday: false };
    });
}

/* The weekday heads of the sheet in the player's language, starting on
   Monday the way a German wall calendar does. */

export function weekdayHeads(locale: string): string[] {
    const format = new Intl.DateTimeFormat(locale, {
        weekday: 'short',
        timeZone: 'UTC',
    });

    return Array.from({ length: WEEK_LENGTH }, (_, day) =>
        format.format(new Date(A_MONDAY + day * 86_400_000)),
    );
}
