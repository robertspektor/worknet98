import { useTranslation } from '@/i18n/use-translation';
import type { CaseLayout } from '../../hardware/case-layout';
import type { SwapAction, SwapState } from '../../hardware/cpu-swap-state';
import { CpuChipArt } from '../cpu-chip-art';
import { useDragPart } from './use-drag-part';
import { useHold } from './use-hold';
import { useWipe } from './use-wipe';

type Act = (action: SwapAction) => void;

function NewCpu({ state, act }: { state: SwapState; act: Act }) {
    const { t } = useTranslation();
    const drag = useDragPart((target) => {
        if (target === 'socket') {
            act({ type: 'seat-cpu' });
        }
    });
    const isActive = state.step === 'insert-cpu';

    return (
        <span
            className={`mat-cpu is-new ${isActive ? 'is-active' : ''}`}
            title={t('workbench.rotate')}
            onClick={() => {
                if (!drag.wasDragged()) {
                    act({ type: 'rotate-cpu' });
                }
            }}
            {...(isActive ? drag.dragProps : {})}
        >
            <span
                className="mat-cpu-turn"
                style={{ rotate: `${state.cpuRotation}deg` }}
            >
                <CpuChipArt />
            </span>
        </span>
    );
}

function Cloth({ state, act }: { state: SwapState; act: Act }) {
    const { t } = useTranslation();
    const { wipeProps } = useWipe((amount) => act({ type: 'wipe', amount }));

    return (
        <span
            className={`tool-cloth ${state.step === 'clean' ? 'is-active' : ''}`}
            title={t('workbench.cloth')}
            {...wipeProps}
        />
    );
}

function PasteTube({ state, act }: { state: SwapState; act: Act }) {
    const { t } = useTranslation();
    const { isHolding, holdProps } = useHold(
        (amount) => act({ type: 'squeeze', amount }),
        () => act({ type: 'release-paste' }),
    );

    return (
        <span
            className={`tool-paste ${isHolding ? 'is-squeezed' : ''} ${state.step === 'paste' ? 'is-active' : ''}`}
            title={t('workbench.paste')}
            {...holdProps}
        >
            <span className="tool-paste-cap" aria-hidden="true" />
            <span className="tool-paste-label">
                {t('workbench.paste_label')}
            </span>
        </span>
    );
}

function LyingCooler({ state, act }: { state: SwapState; act: Act }) {
    const drag = useDragPart((target) => {
        if (target === 'socket' || target === 'case') {
            act({ type: 'move-cooler' });
        }
    });
    const isActive = state.step === 'place-cooler' || state.step === 'paste';

    return (
        <div
            className={`cooler is-lying ${isActive ? 'is-active' : ''}`}
            {...(isActive ? drag.dragProps : {})}
        >
            <span className="cooler-fins" aria-hidden="true" />
            <span className="cooler-fan" aria-hidden="true" />
        </div>
    );
}

function LyingPanel({ state, act }: { state: SwapState; act: Act }) {
    const drag = useDragPart((target) => {
        if (target === 'case' || target === 'socket') {
            act({ type: 'slide-panel' });
        }
    });
    const isActive = state.step === 'attach-panel';

    return (
        <div
            className={`case-panel is-lying ${isActive ? 'is-active' : ''}`}
            {...(isActive ? drag.dragProps : {})}
        >
            <span className="case-panel-vents" aria-hidden="true" />
        </div>
    );
}

export function ToolTray({
    layout,
    state,
    act,
}: {
    layout: CaseLayout;
    state: SwapState;
    act: Act;
}) {
    return (
        <div className="tool-tray">
            <div className="parts-pile">
                {!layout.isPanelOn && <LyingPanel state={state} act={act} />}
                {!layout.isCoolerOnBoard && (
                    <LyingCooler state={state} act={act} />
                )}
            </div>
            <div className="anti-static-mat" data-drop="mat">
                {layout.matCpus.map((kind) =>
                    kind === 'new' ? (
                        <NewCpu key={kind} state={state} act={act} />
                    ) : (
                        <span key={kind} className="mat-cpu is-old">
                            <CpuChipArt isUsed />
                        </span>
                    ),
                )}
            </div>
            <div className="tool-shelf">
                <Cloth state={state} act={act} />
                <PasteTube state={state} act={act} />
            </div>
        </div>
    );
}
