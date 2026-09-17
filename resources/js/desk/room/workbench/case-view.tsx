import { useTranslation } from '@/i18n/use-translation';
import type { CaseLayout } from '../../hardware/case-layout';
import type { SwapAction, SwapState } from '../../hardware/cpu-swap-state';
import { SCREW_COUNT } from '../../hardware/cpu-swap-state';
import { CpuChipArt } from '../cpu-chip-art';
import type { DropTarget } from './use-drag-part';
import { useDragPart } from './use-drag-part';

type Act = (action: SwapAction) => void;

function SidePanel({ state, act }: { state: SwapState; act: Act }) {
    const { t } = useTranslation();
    const drag = useDragPart((target) => {
        if (target !== 'case' && state.step === 'remove-panel') {
            act({ type: 'slide-panel' });
        }
    });

    return (
        <div
            className={`case-panel ${state.step === 'remove-panel' ? 'is-active' : ''}`}
            {...drag.dragProps}
        >
            <span className="case-panel-vents" aria-hidden="true" />
            {Array.from({ length: SCREW_COUNT }, (_, index) => (
                <button
                    key={index}
                    type="button"
                    className={`case-screw is-${index} ${state.tightScrews[index] ? 'is-tight' : 'is-loose'}`}
                    aria-label={t('workbench.screw')}
                    onPointerDown={(event) => event.stopPropagation()}
                    onClick={() => act({ type: 'turn-screw', screw: index })}
                />
            ))}
        </div>
    );
}

function Cooler({
    layout,
    state,
    act,
}: {
    layout: CaseLayout;
    state: SwapState;
    act: Act;
}) {
    const { t } = useTranslation();
    const drag = useDragPart((target: DropTarget | null) => {
        if (target !== 'socket') {
            act({ type: 'move-cooler' });
        }
    });
    const isActive = state.step === 'lift-cooler';

    return (
        <div
            className={`cooler is-mounted ${isActive ? 'is-active' : ''}`}
            {...(isActive ? drag.dragProps : {})}
        >
            <span className="cooler-fins" aria-hidden="true" />
            <span className="cooler-fan" aria-hidden="true" />
            {(['left', 'right'] as const).map((side) => (
                <button
                    key={side}
                    type="button"
                    className={`cooler-clip is-${side} ${layout.areClipsClosed ? 'is-closed' : 'is-open'}`}
                    aria-label={t('workbench.clip')}
                    onPointerDown={(event) => event.stopPropagation()}
                    onClick={() => act({ type: 'flip-clip' })}
                />
            ))}
        </div>
    );
}

function SocketCpu({
    kind,
    state,
    act,
}: {
    kind: 'old' | 'new';
    state: SwapState;
    act: Act;
}) {
    const drag = useDragPart((target) => {
        if (target === 'mat') {
            act({ type: 'take-cpu' });
        }
    });
    const isActive = state.step === 'remove-cpu';

    return (
        <span
            className={`socket-cpu ${isActive ? 'is-active' : ''}`}
            {...(isActive ? drag.dragProps : {})}
        >
            <CpuChipArt isUsed={kind === 'old'} />
        </span>
    );
}

export function CaseView({
    layout,
    state,
    act,
    onPlug,
}: {
    layout: CaseLayout;
    state: SwapState;
    act: Act;
    onPlug: () => void;
}) {
    const { t } = useTranslation();
    const pasteSize = Math.min(state.paste, 2);

    return (
        <div className={`pc-case-open is-step-${state.step}`} data-drop="case">
            <div className="motherboard">
                <span className="motherboard-traces" aria-hidden="true" />
                <span className="ram-slots" aria-hidden="true" />
                <div className="cpu-socket" data-drop="socket">
                    <span className="socket-pins" aria-hidden="true" />
                    {layout.socketCpu && (
                        <SocketCpu
                            kind={layout.socketCpu}
                            state={state}
                            act={act}
                        />
                    )}
                    <span
                        className="socket-dust"
                        style={{
                            opacity:
                                layout.socketCpu && layout.dust > 0
                                    ? 0.35 + layout.dust * 0.65
                                    : 0,
                        }}
                        aria-hidden="true"
                    />
                    {pasteSize > 0 && !layout.isCoolerOnBoard && (
                        <span
                            className="socket-paste"
                            style={{ scale: `${0.4 + pasteSize * 0.6}` }}
                            aria-hidden="true"
                        />
                    )}
                    <button
                        type="button"
                        className={`socket-lever ${layout.isLeverOpen ? 'is-open' : ''} ${state.step === 'open-lever' || state.step === 'close-lever' ? 'is-active' : ''}`}
                        aria-label={t('workbench.lever')}
                        onClick={() => act({ type: 'flip-lever' })}
                    />
                </div>
                {layout.isCoolerOnBoard && (
                    <Cooler layout={layout} state={state} act={act} />
                )}
            </div>
            {layout.isPanelOn && <SidePanel state={state} act={act} />}
            <button
                type="button"
                className={`power-plug ${layout.isPlugged ? 'is-plugged' : 'is-unplugged'} ${state.step === 'unplug' || state.step === 'plug-in' ? 'is-active' : ''}`}
                aria-label={t('workbench.plug')}
                onClick={onPlug}
            >
                <span className="power-plug-cable" aria-hidden="true" />
                <span className="power-plug-head" aria-hidden="true" />
            </button>
        </div>
    );
}
