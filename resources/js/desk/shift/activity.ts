export const HEARTBEAT_INTERVAL_MS = 60_000;

export const IDLE_AFTER_MS = 5 * 60_000;

export function isActive(lastInputMs: number, nowMs: number): boolean {
    return nowMs - lastInputMs < IDLE_AFTER_MS;
}
