const FIRST_HOUR = 7;
const LAST_HOUR = 18;

export function upcomingDates(today: Date, count: number): string[] {
    return Array.from({ length: count }, (_, offset) => {
        const date = new Date(
            today.getFullYear(),
            today.getMonth(),
            today.getDate() + offset,
        );

        return [
            date.getFullYear(),
            String(date.getMonth() + 1).padStart(2, '0'),
            String(date.getDate()).padStart(2, '0'),
        ].join('-');
    });
}

export function timeOptions(): string[] {
    return Array.from(
        { length: (LAST_HOUR - FIRST_HOUR) * 2 + 1 },
        (_, index) => {
            const hour = FIRST_HOUR + Math.floor(index / 2);

            return `${String(hour).padStart(2, '0')}:${index % 2 === 0 ? '00' : '30'}`;
        },
    );
}
