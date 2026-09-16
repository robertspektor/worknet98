import type { ReactNode } from 'react';
import { createContext, use, useReducer } from 'react';
import { APPS } from '../apps/app-registry';
import type { AppId } from '../apps/app-registry';
import { DESKTOP_BOUNDS } from '../screen/viewport';
import type { Position, WindowState } from './window-state';
import {
    focusedWindowId,
    initialWindowState,
    windowReducer,
} from './window-state';

type WindowManager = {
    state: WindowState;
    focusedId: string | null;
    open: (id: AppId) => void;
    focus: (id: string) => void;
    minimize: (id: string) => void;
    close: (id: string) => void;
    move: (id: string, position: Position) => void;
};

const WindowManagerContext = createContext<WindowManager | null>(null);

export function WindowManagerProvider({ children }: { children: ReactNode }) {
    const [state, dispatch] = useReducer(windowReducer, initialWindowState);

    const manager: WindowManager = {
        state,
        focusedId: focusedWindowId(state),
        open: (id) =>
            dispatch({
                type: 'open',
                id,
                size: APPS[id].size,
                bounds: DESKTOP_BOUNDS,
            }),
        focus: (id) => dispatch({ type: 'focus', id }),
        minimize: (id) => dispatch({ type: 'minimize', id }),
        close: (id) => dispatch({ type: 'close', id }),
        move: (id, position) => dispatch({ type: 'move', id, position }),
    };

    return (
        <WindowManagerContext value={manager}>{children}</WindowManagerContext>
    );
}

export function useWindowManager(): WindowManager {
    const manager = use(WindowManagerContext);

    if (!manager) {
        throw new Error(
            'useWindowManager must be used inside WindowManagerProvider.',
        );
    }

    return manager;
}
