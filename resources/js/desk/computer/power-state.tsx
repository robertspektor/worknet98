import type { ReactNode } from 'react';
import { createContext, use, useEffect, useState } from 'react';

type PowerState = {
    isPoweredOn: boolean;
    setPoweredOn: (isPoweredOn: boolean) => void;
};

const PowerStateContext = createContext<PowerState | null>(null);

export function PowerStateProvider({ children }: { children: ReactNode }) {
    const [isPoweredOn, setPoweredOn] = useState(false);

    return (
        <PowerStateContext value={{ isPoweredOn, setPoweredOn }}>
            {children}
        </PowerStateContext>
    );
}

export function useReportPower(isPoweredOn: boolean): void {
    const setPoweredOn = use(PowerStateContext)?.setPoweredOn;

    useEffect(() => {
        setPoweredOn?.(isPoweredOn);
    }, [setPoweredOn, isPoweredOn]);
}

export function useIsPoweredOn(): boolean {
    return use(PowerStateContext)?.isPoweredOn ?? false;
}
