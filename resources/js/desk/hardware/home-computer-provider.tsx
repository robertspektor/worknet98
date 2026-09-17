import type { ReactNode } from 'react';
import { useEffect, useState } from 'react';
import type { HomeComputer } from '@/types';
import { createDeskContext } from '../state/create-desk-context';
import { fetchHomeComputer } from './home-computer-api';

type HomeComputerState = {
    homeComputer: HomeComputer | null;
    refresh: () => void;
    replace: (homeComputer: HomeComputer) => void;
};

const { Context, useRequired } =
    createDeskContext<HomeComputerState>('HomeComputer');

export function HomeComputerProvider({
    isSignedIn,
    children,
}: {
    isSignedIn: boolean;
    children: ReactNode;
}) {
    const [homeComputer, setHomeComputer] = useState<HomeComputer | null>(null);

    const refresh = () =>
        void fetchHomeComputer()
            .then(setHomeComputer)
            .catch(() => undefined);

    useEffect(() => {
        if (isSignedIn) {
            refresh();
        }
    }, [isSignedIn]);

    return (
        <Context value={{ homeComputer, refresh, replace: setHomeComputer }}>
            {children}
        </Context>
    );
}

export const useHomeComputer = useRequired;
