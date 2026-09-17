import { describe, expect, it } from 'vite-plus/test';
import type { SwapState, SwapStep } from './cpu-swap-state';
import { startSwap } from './cpu-swap-state';
import { caseLayout } from './case-layout';

function at(step: SwapStep, goal: SwapState['goal'] = 'swap'): SwapState {
    return { ...startSwap(goal), step };
}

describe('caseLayout', () => {
    it('starts with a closed and plugged in computer', () => {
        expect(caseLayout(at('unplug'))).toMatchObject({
            isPlugged: true,
            isPanelOn: true,
            isCoolerOnBoard: true,
            areClipsClosed: true,
            socketCpu: 'old',
            matCpus: ['new'],
        });
    });

    it('shows an empty socket with both processors on the mat during the swap', () => {
        expect(caseLayout(at('insert-cpu'))).toMatchObject({
            isPlugged: false,
            isPanelOn: false,
            isCoolerOnBoard: false,
            isLeverOpen: true,
            socketCpu: null,
            matCpus: ['old', 'new'],
        });
    });

    it('seats the new processor once the lever is closed', () => {
        expect(caseLayout(at('clean'))).toMatchObject({
            isLeverOpen: false,
            socketCpu: 'new',
            matCpus: ['old'],
            dust: 1,
        });
    });

    it('keeps the old processor in the socket when only the paste is renewed', () => {
        expect(caseLayout(at('paste', 'repaste'))).toMatchObject({
            socketCpu: 'old',
            matCpus: [],
            isLeverOpen: false,
            dust: 0,
        });
    });

    it('is closed and plugged in again when done', () => {
        expect(caseLayout(at('done'))).toMatchObject({
            isPlugged: true,
            isPanelOn: true,
            isCoolerOnBoard: true,
            areClipsClosed: true,
        });
    });
});
