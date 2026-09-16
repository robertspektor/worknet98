export type Size = { width: number; height: number };

export type Position = { x: number; y: number };

export type WindowEntry = {
    id: string;
    size: Size;
    position: Position;
    zIndex: number;
    minimized: boolean;
};

export type WindowState = {
    windows: WindowEntry[];
    topZIndex: number;
};

export type WindowAction =
    | { type: 'open'; id: string; size: Size; bounds: Size }
    | { type: 'focus'; id: string }
    | { type: 'minimize'; id: string }
    | { type: 'close'; id: string }
    | { type: 'move'; id: string; position: Position };

const CASCADE_STEP = 24;
const FIRST_POSITION: Position = { x: 110, y: 16 };

export const initialWindowState: WindowState = { windows: [], topZIndex: 0 };

export function focusedWindowId(state: WindowState): string | null {
    const visible = state.windows.filter((entry) => !entry.minimized);

    return visible.toSorted((a, b) => b.zIndex - a.zIndex).at(0)?.id ?? null;
}

export function windowReducer(
    state: WindowState,
    action: WindowAction,
): WindowState {
    switch (action.type) {
        case 'open':
            return state.windows.some((entry) => entry.id === action.id)
                ? raise(state, action.id)
                : openWindow(state, action.id, action.size, action.bounds);
        case 'focus':
            return raise(state, action.id);
        case 'minimize':
            return update(state, action.id, { minimized: true });
        case 'close':
            return {
                ...state,
                windows: state.windows.filter(
                    (entry) => entry.id !== action.id,
                ),
            };
        case 'move':
            return update(state, action.id, { position: action.position });
    }
}

function openWindow(
    state: WindowState,
    id: string,
    requested: Size,
    bounds: Size,
): WindowState {
    const size = {
        width: Math.min(requested.width, bounds.width),
        height: Math.min(requested.height, bounds.height),
    };
    const offset = state.windows.length * CASCADE_STEP;
    const position = {
        x: clamp(FIRST_POSITION.x + offset, 0, bounds.width - size.width),
        y: clamp(FIRST_POSITION.y + offset, 0, bounds.height - size.height),
    };
    const topZIndex = state.topZIndex + 1;

    return {
        topZIndex,
        windows: [
            ...state.windows,
            { id, size, position, zIndex: topZIndex, minimized: false },
        ],
    };
}

function raise(state: WindowState, id: string): WindowState {
    const topZIndex = state.topZIndex + 1;

    return {
        ...update(state, id, { zIndex: topZIndex, minimized: false }),
        topZIndex,
    };
}

function update(
    state: WindowState,
    id: string,
    changes: Partial<WindowEntry>,
): WindowState {
    return {
        ...state,
        windows: state.windows.map((entry) =>
            entry.id === id ? { ...entry, ...changes } : entry,
        ),
    };
}

export function clamp(value: number, min: number, max: number): number {
    return Math.max(min, Math.min(max, value));
}
