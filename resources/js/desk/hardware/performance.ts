const TURBO_MHZ = 200;

function slowness(speedMhz: number): number {
    return Math.max(0, TURBO_MHZ / speedMhz - 1);
}

function scaled(baseMs: number, extraMs: number, speedMhz: number): number {
    return Math.round(baseMs + extraMs * slowness(speedMhz));
}

export function biosLineDelayMs(speedMhz: number): number {
    return scaled(150, 90, speedMhz);
}

export function memoryCountDurationMs(speedMhz: number): number {
    return scaled(300, 900, speedMhz);
}

export function splashStatusDelayMs(speedMhz: number): number {
    return scaled(650, 250, speedMhz);
}

export function appLaunchDelayMs(speedMhz: number): number {
    return scaled(0, 900, speedMhz);
}

export function pageLoadDurationMs(speedMhz: number): number {
    return scaled(250, 900, speedMhz);
}
