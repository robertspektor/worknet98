export function formatDateTime(iso: string, locale: string): string {
    return new Date(iso).toLocaleString(locale, {
        day: '2-digit',
        month: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    });
}

export function formatDate(iso: string, locale: string): string {
    return new Date(iso).toLocaleDateString(locale, {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

export function formatDay(isoDate: string, locale: string): string {
    return new Date(`${isoDate}T00:00:00`).toLocaleDateString(locale, {
        weekday: 'short',
        day: '2-digit',
        month: '2-digit',
    });
}

export function formatAmount(amount: number, locale: string): string {
    return amount.toLocaleString(locale);
}

export function formatTime(iso: string, locale: string): string {
    return new Date(iso).toLocaleTimeString(locale, {
        hour: '2-digit',
        minute: '2-digit',
    });
}
