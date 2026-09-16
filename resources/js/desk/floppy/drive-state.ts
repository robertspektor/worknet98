export type DrivePhase = 'empty' | 'inserting' | 'loaded' | 'ejecting';

export type DriveState = {
    phase: DrivePhase;
    diskId: number | null;
};

export type DriveAction =
    | { type: 'insert'; diskId: number }
    | { type: 'eject' }
    | { type: 'settle' };

export const emptyDrive: DriveState = { phase: 'empty', diskId: null };

export function driveReducer(
    state: DriveState,
    action: DriveAction,
): DriveState {
    switch (action.type) {
        case 'insert':
            return state.phase === 'empty'
                ? { phase: 'inserting', diskId: action.diskId }
                : state;
        case 'eject':
            return state.phase === 'loaded'
                ? { ...state, phase: 'ejecting' }
                : state;
        case 'settle':
            return settle(state);
    }
}

function settle(state: DriveState): DriveState {
    switch (state.phase) {
        case 'inserting':
            return { ...state, phase: 'loaded' };
        case 'ejecting':
            return emptyDrive;
        default:
            return state;
    }
}

export function loadedDiskId(state: DriveState): number | null {
    return state.phase === 'loaded' ? state.diskId : null;
}
