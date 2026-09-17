export type SwapGoal = 'swap' | 'repaste';

export type SwapStep =
    | 'unplug'
    | 'unscrew'
    | 'remove-panel'
    | 'unclip-cooler'
    | 'lift-cooler'
    | 'open-lever'
    | 'remove-cpu'
    | 'insert-cpu'
    | 'close-lever'
    | 'clean'
    | 'paste'
    | 'place-cooler'
    | 'clip-cooler'
    | 'attach-panel'
    | 'screw'
    | 'plug-in'
    | 'done';

export type SwapHint =
    | 'zap'
    | 'wrong-orientation'
    | 'dirty'
    | 'too-little-paste'
    | 'too-much-paste'
    | 'no-paste';

export type SwapState = {
    goal: SwapGoal;
    step: SwapStep;
    tightScrews: boolean[];
    cleaned: number;
    paste: number;
    cpuRotation: number;
    pasteApplied: boolean;
    hint: SwapHint | null;
};

export type SwapAction =
    | { type: 'pull-plug'; isPoweredOn: boolean }
    | { type: 'turn-screw'; screw: number }
    | { type: 'slide-panel' }
    | { type: 'flip-clip' }
    | { type: 'move-cooler' }
    | { type: 'flip-lever' }
    | { type: 'take-cpu' }
    | { type: 'rotate-cpu' }
    | { type: 'seat-cpu' }
    | { type: 'wipe'; amount: number }
    | { type: 'squeeze'; amount: number }
    | { type: 'release-paste' }
    | { type: 'push-plug' };

export const SCREW_COUNT = 4;
export const MIN_PASTE = 0.35;
export const MAX_PASTE = 1.4;

const STEP_ORDER: SwapStep[] = [
    'unplug',
    'unscrew',
    'remove-panel',
    'unclip-cooler',
    'lift-cooler',
    'open-lever',
    'remove-cpu',
    'insert-cpu',
    'close-lever',
    'clean',
    'paste',
    'place-cooler',
    'clip-cooler',
    'attach-panel',
    'screw',
    'plug-in',
    'done',
];

const CPU_STEPS: SwapStep[] = [
    'open-lever',
    'remove-cpu',
    'insert-cpu',
    'close-lever',
];

const RIGHT_ANGLE = 90;
const FULL_TURN = 360;

export function startSwap(goal: SwapGoal): SwapState {
    return {
        goal,
        step: 'unplug',
        tightScrews: Array.from({ length: SCREW_COUNT }, () => true),
        cleaned: 0,
        paste: 0,
        cpuRotation: RIGHT_ANGLE,
        pasteApplied: false,
        hint: null,
    };
}

export function stepsFor(goal: SwapGoal): SwapStep[] {
    return goal === 'swap'
        ? STEP_ORDER
        : STEP_ORDER.filter((step) => !CPU_STEPS.includes(step));
}

function stepAfter(state: SwapState, step: SwapStep = state.step): SwapStep {
    const steps = stepsFor(state.goal);

    return steps[steps.indexOf(step) + 1] ?? 'done';
}

function advance(
    state: SwapState,
    changes: Partial<SwapState> = {},
): SwapState {
    return { ...state, ...changes, step: stepAfter(state), hint: null };
}

function withHint(state: SwapState, hint: SwapHint): SwapState {
    return state.hint === hint ? state : { ...state, hint };
}

function turnScrew(state: SwapState, screw: number): SwapState {
    const shouldBeTight = state.step === 'screw';

    if (state.tightScrews[screw] !== !shouldBeTight) {
        return state;
    }

    const tightScrews = state.tightScrews.map((isTight, index) =>
        index === screw ? shouldBeTight : isTight,
    );
    const isFinished = tightScrews.every(
        (isTight) => isTight === shouldBeTight,
    );

    return isFinished
        ? advance(state, { tightScrews })
        : { ...state, tightScrews, hint: null };
}

function wipe(state: SwapState, amount: number): SwapState {
    const cleaned = Math.min(1, state.cleaned + amount);

    return cleaned >= 1
        ? advance(state, { cleaned })
        : { ...state, cleaned, hint: null };
}

function releasePaste(state: SwapState): SwapState {
    if (state.paste === 0) {
        return state;
    }

    if (state.paste < MIN_PASTE) {
        return withHint(state, 'too-little-paste');
    }

    return {
        ...advance(state, { pasteApplied: true }),
        hint: state.paste > MAX_PASTE ? 'too-much-paste' : null,
    };
}

function placeCoolerDuringPaste(state: SwapState): SwapState {
    const pasteApplied = state.paste >= MIN_PASTE;

    return {
        ...state,
        step: stepAfter(state, 'place-cooler'),
        pasteApplied,
        hint: pasteApplied ? null : 'no-paste',
    };
}

function seatCpu(state: SwapState): SwapState {
    return state.cpuRotation === 0
        ? advance(state)
        : withHint(state, 'wrong-orientation');
}

export function cpuSwapReducer(
    state: SwapState,
    action: SwapAction,
): SwapState {
    switch (`${state.step}:${action.type}`) {
        case 'unplug:pull-plug':
            return action.type === 'pull-plug' && action.isPoweredOn
                ? withHint(state, 'zap')
                : advance(state);
        case 'unscrew:turn-screw':
        case 'screw:turn-screw':
            return action.type === 'turn-screw'
                ? turnScrew(state, action.screw)
                : state;
        case 'remove-panel:slide-panel':
        case 'attach-panel:slide-panel':
        case 'unclip-cooler:flip-clip':
        case 'clip-cooler:flip-clip':
        case 'lift-cooler:move-cooler':
        case 'place-cooler:move-cooler':
        case 'open-lever:flip-lever':
        case 'close-lever:flip-lever':
        case 'remove-cpu:take-cpu':
        case 'plug-in:push-plug':
            return advance(state);
        case 'insert-cpu:rotate-cpu':
            return {
                ...state,
                cpuRotation: (state.cpuRotation + RIGHT_ANGLE) % FULL_TURN,
                hint: null,
            };
        case 'insert-cpu:seat-cpu':
            return seatCpu(state);
        case 'clean:wipe':
            return action.type === 'wipe' ? wipe(state, action.amount) : state;
        case 'clean:squeeze':
            return withHint(state, 'dirty');
        case 'paste:squeeze':
            return action.type === 'squeeze'
                ? { ...state, paste: state.paste + action.amount, hint: null }
                : state;
        case 'paste:release-paste':
            return releasePaste(state);
        case 'paste:move-cooler':
            return placeCoolerDuringPaste(state);
        default:
            return state;
    }
}
