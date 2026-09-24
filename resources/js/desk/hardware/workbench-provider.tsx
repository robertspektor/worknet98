import type { ReactNode } from 'react';
import { useMemo, useState } from 'react';
import type { DeskPart, HomeComputer } from '@/types';
import { TowerWorkbench } from '../room/workbench/tower-workbench';
import { createDeskContext } from '../state/create-desk-context';
import type { SwapGoal } from './cpu-swap-state';
import { useHomeComputer } from './home-computer-provider';

const FINISH_DELAY_MS = 1800;

export type Workbench = {
    /* What is waiting inside the case, if anything: a processor lying on the
       desk wants to be installed, a dried out one wants fresh paste. With
       nothing to do the case can still be opened, to look. */
    job: SwapGoal | null;
    open: () => void;
};

const { Context, useRequired } = createDeskContext<Workbench>('Workbench');

export const useWorkbench = useRequired;

function jobFor(newCpu: DeskPart | null, needsPaste: boolean): SwapGoal | null {
    if (newCpu) {
        return 'swap';
    }

    return needsPaste ? 'repaste' : null;
}

/* The open case belongs to the whole desk, not to one thing on it: the
   screwdriver on the desk and the menu of the tower open the same bench, and
   it hangs outside the camera so no shot can scale it. */

export function WorkbenchProvider({ children }: { children: ReactNode }) {
    const { homeComputer, replace } = useHomeComputer();
    const [isOpen, setOpen] = useState(false);
    const newCpu =
        homeComputer?.desk_parts.find((part) => !part.is_used) ?? null;
    const job = jobFor(newCpu, homeComputer?.cpu.needs_thermal_paste ?? false);

    const workbench = useMemo(
        () => ({ job, open: () => setOpen(true) }),
        [job],
    );

    const finish = (updated: HomeComputer | null) =>
        setTimeout(() => {
            if (updated) {
                replace(updated);
            }

            setOpen(false);
        }, FINISH_DELAY_MS);

    return (
        <Context value={workbench}>
            {children}
            {isOpen && (
                <TowerWorkbench
                    goal={job ?? 'look'}
                    newCpu={newCpu}
                    onClose={() => setOpen(false)}
                    onFinished={finish}
                />
            )}
        </Context>
    );
}
