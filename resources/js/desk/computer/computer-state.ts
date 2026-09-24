export type ComputerState =
    | 'off'
    | 'booting'
    | 'ready'
    | 'shut-down'
    | 'thermal-fault';

export type ComputerEvent =
    | 'power'
    | 'reset'
    | 'boot-finished'
    | 'shut-down'
    | 'overheat';

export function nextComputerState(
    state: ComputerState,
    event: ComputerEvent,
): ComputerState {
    if (event === 'power') {
        return state === 'off' ? 'booting' : 'off';
    }

    if (event === 'reset') {
        return state === 'off' ? state : 'booting';
    }

    if (event === 'boot-finished' && state === 'booting') {
        return 'ready';
    }

    if (event === 'shut-down' && state === 'ready') {
        return 'shut-down';
    }

    if (event === 'overheat' && state === 'ready') {
        return 'thermal-fault';
    }

    return state;
}
