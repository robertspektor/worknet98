import type { ReactNode } from 'react';
import { createContext, use, useEffect, useState } from 'react';
import type { HomeComputer } from '@/types';
import { fetchHomeComputer } from './home-computer-api';

type HomeComputerState = {
    homeComputer: HomeComputer | null;
    refresh: () => void;
    replace: (homeComputer: HomeComputer) => void;
};

const HomeComputerContext = createContext<HomeComputerState | null>(null);

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
        <HomeComputerContext
            value={{ homeComputer, refresh, replace: setHomeComputer }}
        >
            {children}
        </HomeComputerContext>
    );
}

export function useHomeComputer(): HomeComputerState {
    const state = use(HomeComputerContext);

    if (!state) {
        throw new Error(
            'useHomeComputer must be used inside HomeComputerProvider.',
        );
    }

    return state;
}
