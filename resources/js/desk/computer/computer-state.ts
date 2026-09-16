export type ComputerState = 'off' | 'booting' | 'ready' | 'shut-down';

export type ComputerEvent = 'power' | 'boot-finished' | 'shut-down';

export function nextComputerState(
    state: ComputerState,
    event: ComputerEvent,
): ComputerState {
    if (event === 'power') {
        return state === 'off' ? 'booting' : 'off';
    }

    if (event === 'boot-finished' && state === 'booting') {
        return 'ready';
    }

    if (event === 'shut-down' && state === 'ready') {
        return 'shut-down';
    }

    return state;
}
