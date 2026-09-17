import { describe, expect, it } from 'vite-plus/test';
import type { SwapAction, SwapState } from './cpu-swap-state';
import {
    cpuSwapReducer,
    MAX_PASTE,
    MIN_PASTE,
    SCREW_COUNT,
    startSwap,
} from './cpu-swap-state';

function run(state: SwapState, ...actions: SwapAction[]): SwapState {
    return actions.reduce(cpuSwapReducer, state);
}

const screws = (): SwapAction[] =>
    Array.from({ length: SCREW_COUNT }, (_, screw) => ({
        type: 'turn-screw',
        screw,
    }));

const openCase = (): SwapAction[] => [
    { type: 'pull-plug', isPoweredOn: false },
    ...screws(),
    { type: 'slide-panel' },
    { type: 'flip-clip' },
    { type: 'move-cooler' },
];

const replaceCpu = (): SwapAction[] => [
    { type: 'flip-lever' },
    { type: 'take-cpu' },
    { type: 'rotate-cpu' },
    { type: 'rotate-cpu' },
    { type: 'rotate-cpu' },
    { type: 'seat-cpu' },
    { type: 'flip-lever' },
];

const cleanAndPaste = (amount = 0.8): SwapAction[] => [
    { type: 'wipe', amount: 0.6 },
    { type: 'wipe', amount: 0.6 },
    { type: 'squeeze', amount },
    { type: 'release-paste' },
];

const closeCase = (): SwapAction[] => [
    { type: 'move-cooler' },
    { type: 'flip-clip' },
    { type: 'slide-panel' },
    ...screws(),
    { type: 'push-plug' },
];

describe('cpuSwapReducer', () => {
    it('walks through a complete processor swap', () => {
        const state = run(
            startSwap('swap'),
            ...openCase(),
            ...replaceCpu(),
            ...cleanAndPaste(),
            ...closeCase(),
        );

        expect(state).toMatchObject({
            step: 'done',
            pasteApplied: true,
            tightScrews: [true, true, true, true],
            hint: null,
        });
    });

    it('skips the processor steps when only the paste is renewed', () => {
        const opened = run(startSwap('repaste'), ...openCase());
        expect(opened.step).toBe('clean');

        const state = run(opened, ...cleanAndPaste(), ...closeCase());
        expect(state).toMatchObject({ step: 'done', pasteApplied: true });
    });

    it('zaps the player who unplugs a running computer', () => {
        const state = run(startSwap('swap'), {
            type: 'pull-plug',
            isPoweredOn: true,
        });

        expect(state).toMatchObject({ step: 'unplug', hint: 'zap' });
    });

    it('loosens exactly the screw that was turned, and each only once', () => {
        const state = run(
            startSwap('swap'),
            { type: 'pull-plug', isPoweredOn: false },
            { type: 'turn-screw', screw: 2 },
            { type: 'turn-screw', screw: 2 },
        );

        expect(state).toMatchObject({
            step: 'unscrew',
            tightScrews: [true, true, false, true],
        });
    });

    it('does not seat a processor that is turned the wrong way', () => {
        const ready = run(
            startSwap('swap'),
            ...openCase(),
            { type: 'flip-lever' },
            { type: 'take-cpu' },
        );

        const state = run(ready, { type: 'seat-cpu' });

        expect(state).toMatchObject({
            step: 'insert-cpu',
            hint: 'wrong-orientation',
        });
    });

    it('wants a clean surface before the paste goes on', () => {
        const state = run(startSwap('repaste'), ...openCase(), {
            type: 'squeeze',
            amount: 0.5,
        });

        expect(state).toMatchObject({ step: 'clean', hint: 'dirty', paste: 0 });
    });

    it('asks for more paste when there is too little', () => {
        const state = run(
            startSwap('repaste'),
            ...openCase(),
            ...cleanAndPaste(MIN_PASTE / 2),
        );

        expect(state).toMatchObject({
            step: 'paste',
            hint: 'too-little-paste',
        });
        expect(
            run(
                state,
                { type: 'squeeze', amount: MIN_PASTE },
                {
                    type: 'release-paste',
                },
            ).step,
        ).toBe('place-cooler');
    });

    it('comments on too much paste but accepts it', () => {
        const state = run(
            startSwap('repaste'),
            ...openCase(),
            ...cleanAndPaste(MAX_PASTE + 1),
        );

        expect(state).toMatchObject({
            step: 'place-cooler',
            hint: 'too-much-paste',
            pasteApplied: true,
        });
    });

    it('lets the player forget the paste', () => {
        const cleaned = run(startSwap('repaste'), ...openCase(), {
            type: 'wipe',
            amount: 1,
        });

        const state = run(cleaned, { type: 'move-cooler' });

        expect(state).toMatchObject({
            step: 'clip-cooler',
            hint: 'no-paste',
            pasteApplied: false,
        });
    });

    it('ignores actions that do not fit the current step', () => {
        const state = startSwap('swap');

        expect(cpuSwapReducer(state, { type: 'take-cpu' })).toBe(state);
        expect(cpuSwapReducer(state, { type: 'turn-screw', screw: 0 })).toBe(
            state,
        );
    });
});
