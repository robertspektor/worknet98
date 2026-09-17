import { useState } from 'react';
import type { ComputerEvent, ComputerState } from './computer-state';
import { nextComputerState } from './computer-state';

export function useComputer(initialState: ComputerState = 'off') {
    const [state, setState] = useState<ComputerState>(initialState);
    const send = (event: ComputerEvent) =>
        setState((current) => nextComputerState(current, event));

    return {
        state,
        isOn: state !== 'off',
        togglePower: () => send('power'),
        finishBoot: () => send('boot-finished'),
        shutDown: () => send('shut-down'),
        overheat: () => send('overheat'),
    };
}
