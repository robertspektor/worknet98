export type BrowserHistory<Page extends string> = {
    pages: Page[];
};

export function startAt<Page extends string>(page: Page): BrowserHistory<Page> {
    return { pages: [page] };
}

export function currentPage<Page extends string>(
    history: BrowserHistory<Page>,
): Page {
    return history.pages[history.pages.length - 1];
}

export function visit<Page extends string>(
    history: BrowserHistory<Page>,
    page: Page,
): BrowserHistory<Page> {
    return currentPage(history) === page
        ? history
        : { pages: [...history.pages, page] };
}

export function goBack<Page extends string>(
    history: BrowserHistory<Page>,
): BrowserHistory<Page> {
    return canGoBack(history) ? { pages: history.pages.slice(0, -1) } : history;
}

export function canGoBack<Page extends string>(
    history: BrowserHistory<Page>,
): boolean {
    return history.pages.length > 1;
}
