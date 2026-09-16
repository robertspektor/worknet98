export class GameApiError extends Error {
    constructor(
        public readonly status: number,
        message: string,
    ) {
        super(message);
    }
}

type ErrorBody = { message?: string };

const XSRF_COOKIE = 'XSRF-TOKEN';

export function getJson<T>(url: string): Promise<T> {
    return request<T>(url, { method: 'GET' });
}

export function postJson<T>(url: string, body: object = {}): Promise<T> {
    return request<T>(
        url,
        { method: 'POST', body: JSON.stringify(body) },
        {
            'Content-Type': 'application/json',
            'X-XSRF-TOKEN': readCookie(XSRF_COOKIE),
        },
    );
}

async function request<T>(
    url: string,
    init: Pick<RequestInit, 'method' | 'body'>,
    headers: Record<string, string> = {},
): Promise<T> {
    const response = await fetch(url, {
        ...init,
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...headers,
        },
    });

    if (!response.ok) {
        throw new GameApiError(response.status, await errorMessage(response));
    }

    return response.status === 204
        ? (undefined as T)
        : ((await response.json()) as T);
}

async function errorMessage(response: Response): Promise<string> {
    const body = (await response.json().catch(() => ({}))) as ErrorBody;

    return (
        body.message ??
        `Game API request failed with status ${response.status}.`
    );
}

function readCookie(name: string): string {
    const entry = document.cookie
        .split('; ')
        .find((cookie) => cookie.startsWith(`${name}=`));

    return entry ? decodeURIComponent(entry.slice(name.length + 1)) : '';
}
