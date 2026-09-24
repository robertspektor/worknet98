import type { ReactNode } from 'react';
import { createDeskContext } from '../state/create-desk-context';
import type { ComputerState } from './computer-state';
import { useComputer } from './use-computer';

type Computer = ReturnType<typeof useComputer>;

const { Context, useRequired } = createDeskContext<Computer>('Computer');

/* The machine lives above the desk: the tower carries its power switch, the
   monitor only shows what it is doing. */

export function ComputerProvider({
    initialState = 'off',
    children,
}: {
    initialState?: ComputerState;
    children: ReactNode;
}) {
    const computer = useComputer(initialState);

    return <Context value={computer}>{children}</Context>;
}

export const useComputerMachine = useRequired;

/* Deliberately not optional: a silent false here would leave the room looking
   switched off while the screen is running. */

export function useIsPoweredOn(): boolean {
    return useRequired().isOn;
}
