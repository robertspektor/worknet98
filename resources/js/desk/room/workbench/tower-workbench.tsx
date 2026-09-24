import { useEffect, useReducer, useRef, useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import type { DeskPart, HomeComputer } from '@/types';
import { useIsPoweredOn } from '../../computer/computer-provider';
import { caseLayout } from '../../hardware/case-layout';
import type { SwapAction, SwapGoal } from '../../hardware/cpu-swap-state';
import { cpuSwapReducer, startSwap } from '../../hardware/cpu-swap-state';
import {
    applyThermalPaste,
    installDeskPart,
} from '../../hardware/home-computer-api';
import { sound } from '../../sound/sound';
import { CaseView } from './case-view';
import { ToolTray } from './tool-tray';

const SOUNDS: Partial<Record<SwapAction['type'], () => void>> = {
    'turn-screw': () => sound.screw(),
    'flip-clip': () => sound.latch(),
    'flip-lever': () => sound.latch(),
    'push-plug': () => sound.fanSpinUp(),
};

function submitWork(
    goal: SwapGoal,
    newCpu: DeskPart | null,
    pasteApplied: boolean,
): Promise<HomeComputer | null> {
    if (goal === 'swap' && newCpu) {
        return installDeskPart(newCpu, pasteApplied);
    }

    return pasteApplied ? applyThermalPaste() : Promise.resolve(null);
}

function useSubmitWhenDone(
    isDone: boolean,
    submit: () => Promise<HomeComputer | null>,
    onSubmitted: (homeComputer: HomeComputer | null) => void,
) {
    const [hasFailed, setFailed] = useState(false);
    const hasSubmittedRef = useRef(false);

    useEffect(() => {
        if (!isDone || hasSubmittedRef.current) {
            return;
        }

        hasSubmittedRef.current = true;
        void submit()
            .then(onSubmitted)
            .catch(() => setFailed(true));
    }, [isDone, submit, onSubmitted]);

    return hasFailed;
}

export function TowerWorkbench({
    goal,
    newCpu,
    onClose,
    onFinished,
}: {
    goal: SwapGoal;
    newCpu: DeskPart | null;
    onClose: () => void;
    onFinished: (homeComputer: HomeComputer | null) => void;
}) {
    const { t } = useTranslation();
    const isPoweredOn = useIsPoweredOn();
    const [state, dispatch] = useReducer(cpuSwapReducer, goal, startSwap);
    const layout = caseLayout(state);
    const hasFailed = useSubmitWhenDone(
        state.step === 'done',
        () => submitWork(goal, newCpu, state.pasteApplied),
        onFinished,
    );

    const act = (action: SwapAction) => {
        SOUNDS[action.type]?.();
        dispatch(action);
    };

    const plug = () => {
        if (state.step === 'unplug' && isPoweredOn) {
            sound.zap();
        }

        act(
            state.step === 'plug-in'
                ? { type: 'push-plug' }
                : { type: 'pull-plug', isPoweredOn },
        );
    };

    useEffect(() => {
        const closeOnEscape = (event: KeyboardEvent) => {
            if (event.key === 'Escape') {
                onClose();
            }
        };
        document.addEventListener('keydown', closeOnEscape);

        return () => document.removeEventListener('keydown', closeOnEscape);
    }, [onClose]);

    return (
        <div className="workbench-backdrop">
            <section
                className="workbench"
                role="dialog"
                aria-label={t('workbench.title')}
            >
                <header className="workbench-header">
                    <div>
                        <p className="workbench-step">
                            {t(`workbench.step.${state.step}`)}
                        </p>
                        <p className="workbench-hint" aria-live="polite">
                            {hasFailed
                                ? t('workbench.hint.failed')
                                : state.hint &&
                                  t(`workbench.hint.${state.hint}`)}
                        </p>
                    </div>
                    <button
                        type="button"
                        className="workbench-close"
                        onClick={onClose}
                    >
                        {t('workbench.close')}
                    </button>
                </header>
                <div className="workbench-table" data-drop="bench">
                    <CaseView
                        layout={layout}
                        state={state}
                        act={act}
                        onPlug={plug}
                    />
                    <ToolTray layout={layout} state={state} act={act} />
                </div>
            </section>
        </div>
    );
}
