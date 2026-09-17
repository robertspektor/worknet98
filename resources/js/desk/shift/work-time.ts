export type WorkTime = {
    workedSeconds: number;
    targetSeconds: number;
    remainingSeconds: number;
    overtimeSeconds: number;
    progress: number;
};

export type Countdown = {
    days: number;
    hours: number;
    minutes: number;
};

export function summarizeWorkTime(
    workedSeconds: number,
    targetSeconds: number,
): WorkTime {
    return {
        workedSeconds,
        targetSeconds,
        remainingSeconds: Math.max(0, targetSeconds - workedSeconds),
        overtimeSeconds: Math.max(0, workedSeconds - targetSeconds),
        progress:
            targetSeconds > 0 ? Math.min(1, workedSeconds / targetSeconds) : 1,
    };
}

export function formatWorkTime(seconds: number): string {
    const totalMinutes = Math.floor(seconds / 60);
    const minutes = String(totalMinutes % 60).padStart(2, '0');

    return `${Math.floor(totalMinutes / 60)}:${minutes}`;
}

export function countdownUntil(targetMs: number, nowMs: number): Countdown {
    const totalMinutes = Math.max(0, Math.ceil((targetMs - nowMs) / 60_000));

    return {
        days: Math.floor(totalMinutes / 1440),
        hours: Math.floor(totalMinutes / 60) % 24,
        minutes: totalMinutes % 60,
    };
}
