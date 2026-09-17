import type { SwapState, SwapStep } from './cpu-swap-state';
import { stepsFor } from './cpu-swap-state';

const ALL_STEPS = stepsFor('swap');

function hasReached(state: SwapState, step: SwapStep): boolean {
    return ALL_STEPS.indexOf(state.step) >= ALL_STEPS.indexOf(step);
}

export type CaseLayout = {
    isPlugged: boolean;
    isPanelOn: boolean;
    isCoolerOnBoard: boolean;
    areClipsClosed: boolean;
    isLeverOpen: boolean;
    socketCpu: 'old' | 'new' | null;
    matCpus: ('old' | 'new')[];
    dust: number;
};

function socketCpu(state: SwapState): CaseLayout['socketCpu'] {
    if (state.goal === 'repaste') {
        return 'old';
    }

    if (!hasReached(state, 'insert-cpu')) {
        return 'old';
    }

    return hasReached(state, 'close-lever') ? 'new' : null;
}

function matCpus(state: SwapState): CaseLayout['matCpus'] {
    if (state.goal === 'repaste') {
        return [];
    }

    return [
        ...(hasReached(state, 'insert-cpu') ? (['old'] as const) : []),
        ...(hasReached(state, 'close-lever') ? [] : (['new'] as const)),
    ];
}

export function caseLayout(state: SwapState): CaseLayout {
    const isCleaning =
        hasReached(state, 'clean') && !hasReached(state, 'paste');

    return {
        isPlugged: !hasReached(state, 'unscrew') || state.step === 'done',
        isPanelOn:
            !hasReached(state, 'unclip-cooler') || hasReached(state, 'screw'),
        isCoolerOnBoard:
            !hasReached(state, 'open-lever') ||
            hasReached(state, 'clip-cooler'),
        areClipsClosed:
            !hasReached(state, 'lift-cooler') ||
            hasReached(state, 'attach-panel'),
        isLeverOpen:
            state.goal === 'swap' &&
            hasReached(state, 'remove-cpu') &&
            !hasReached(state, 'clean'),
        socketCpu: socketCpu(state),
        matCpus: matCpus(state),
        dust: isCleaning || !hasReached(state, 'clean') ? 1 - state.cleaned : 0,
    };
}
