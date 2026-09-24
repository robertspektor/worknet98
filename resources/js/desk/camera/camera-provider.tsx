import { createDeskContext } from '../state/create-desk-context';
import type { CameraView, FocusTarget } from './camera-state';

export type Camera = {
    view: CameraView;
    focus: (target: FocusTarget) => void;
    toRoom: () => void;
};

const { Context, useRequired } = createDeskContext<Camera>('Camera');

export const CameraContext = Context;
export const useCamera = useRequired;

export function useIsFocused(target: FocusTarget): boolean {
    const { view } = useCamera();

    return view.kind === 'detail' && view.target === target;
}
