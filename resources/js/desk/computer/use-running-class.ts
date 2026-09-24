import { useEffect } from 'react';
import { useIsPoweredOn } from './computer-provider';

/* The page around the scene reacts to the machine, so the state goes on the
   body itself rather than being read back out of the tree. */

export function useRunningBodyClass(): boolean {
    const isRunning = useIsPoweredOn();

    useEffect(() => {
        document.body.classList.toggle('is-running', isRunning);

        return () => document.body.classList.remove('is-running');
    }, [isRunning]);

    return isRunning;
}
