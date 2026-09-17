import type { ReactNode } from 'react';
import { useReducer } from 'react';
import { APPS } from '../apps/app-registry';
import type { AppId } from '../apps/app-registry';
import { createDeskContext } from '../state/create-desk-context';
import { DESKTOP_BOUNDS } from '../screen/viewport';
import { useAppLauncher } from './use-app-launcher';
import type { Position, WindowState } from './window-state';
import {
    focusedWindowId,
    initialWindowState,
    windowReducer,
} from './window-state';

type WindowManager = {
    state: WindowState;
    focusedId: string | null;
    isLaunching: boolean;
    open: (id: AppId) => void;
    focus: (id: string) => void;
    minimize: (id: string) => void;
    close: (id: string) => void;
    move: (id: string, position: Position) => void;
};

const { Context, useRequired } =
    createDeskContext<WindowManager>('WindowManager');

export function WindowManagerProvider({ children }: { children: ReactNode }) {
    const [state, dispatch] = useReducer(windowReducer, initialWindowState);
    const openNow = (id: AppId) =>
        dispatch({
            type: 'open',
            id,
            size: APPS[id].size,
            bounds: DESKTOP_BOUNDS,
        });
    const launcher = useAppLauncher(
        (id) => state.windows.some((entry) => entry.id === id),
        openNow,
    );

    const manager: WindowManager = {
        state,
        focusedId: focusedWindowId(state),
        isLaunching: launcher.isLaunching,
        open: launcher.launch,
        focus: (id) => dispatch({ type: 'focus', id }),
        minimize: (id) => dispatch({ type: 'minimize', id }),
        close: (id) => dispatch({ type: 'close', id }),
        move: (id, position) => dispatch({ type: 'move', id, position }),
    };

    return (
        <Context value={manager}>
            <div
                className={
                    launcher.isLaunching ? 'os-pointer is-busy' : 'os-pointer'
                }
            >
                {children}
            </div>
        </Context>
    );
}

export const useWindowManager = useRequired;
