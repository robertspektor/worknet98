const LOADED = 1;

export function pageLoadProgress(
    elapsedMs: number,
    durationMs: number,
): number {
    if (durationMs <= 0) {
        return LOADED;
    }

    return Math.min(LOADED, elapsedMs / durationMs);
}

export function isPageLoaded(progress: number): boolean {
    return progress >= LOADED;
}
