export const INSTALLED_MEMORY_KB = 16_384;

const COUNT_STEP_KB = 1_024;

export function countedMemoryKb(elapsedMs: number, durationMs: number): number {
    if (durationMs <= 0 || elapsedMs >= durationMs) {
        return INSTALLED_MEMORY_KB;
    }

    const counted = (INSTALLED_MEMORY_KB * elapsedMs) / durationMs;

    return Math.floor(counted / COUNT_STEP_KB) * COUNT_STEP_KB;
}
