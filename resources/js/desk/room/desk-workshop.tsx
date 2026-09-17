import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import type { DeskPart } from '@/types';
import type { SwapGoal } from '../hardware/cpu-swap-state';
import { usePlaceable } from '../placement/use-placeable';
import { useHomeComputer } from '../hardware/home-computer-provider';
import { CpuChipArt } from './cpu-chip-art';
import { TowerWorkbench } from './workbench/tower-workbench';

const FINISH_DELAY_MS = 1800;

function DeskPartItem({ part }: { part: DeskPart }) {
    const { t } = useTranslation();
    const name = t(`hardware_part.${part.slug}.label`);
    const state = part.is_used ? 'used' : 'new';
    const { className, ...placeable } = usePlaceable<HTMLDivElement>(
        `desk-part-${part.id}`,
    );

    return (
        <div
            className={`desk-part is-${state} ${className}`}
            {...placeable}
            title={`${t(`desk_part.${state}`)}: ${name}`}
        >
            {part.is_used ? (
                <CpuChipArt isUsed />
            ) : (
                <span className="anti-static-bag">
                    <CpuChipArt />
                    <span className="anti-static-seal" />
                </span>
            )}
            <span className="desk-item-caption">
                {t(`desk_part.${state}_caption`)}
            </span>
        </div>
    );
}

function Screwdriver({ onPickUp }: { onPickUp: () => void }) {
    const { t } = useTranslation();
    const { className, ...placeable } =
        usePlaceable<HTMLButtonElement>('screwdriver');

    return (
        <button
            type="button"
            className={`case-toolkit ${className}`}
            {...placeable}
            title={t('workbench.open')}
            onClick={onPickUp}
        >
            <span className="screwdriver" aria-hidden="true">
                <span className="screwdriver-handle" />
                <span className="screwdriver-shaft" />
            </span>
            <span className="desk-item-caption">{t('workbench.open')}</span>
        </button>
    );
}

export function DeskWorkshop() {
    const { homeComputer, replace } = useHomeComputer();
    const [goal, setGoal] = useState<SwapGoal | null>(null);
    const parts = homeComputer?.desk_parts ?? [];
    const newCpu = parts.find((part) => !part.is_used) ?? null;
    const availableGoal: SwapGoal | null = newCpu
        ? 'swap'
        : homeComputer?.cpu.needs_thermal_paste
          ? 'repaste'
          : null;

    if (parts.length === 0 && availableGoal === null) {
        return null;
    }

    return (
        <>
            <div className="desk-item desk-workshop">
                {availableGoal && (
                    <Screwdriver onPickUp={() => setGoal(availableGoal)} />
                )}
                {parts.map((part) => (
                    <DeskPartItem key={part.id} part={part} />
                ))}
            </div>
            {goal && (
                <TowerWorkbench
                    goal={goal}
                    newCpu={newCpu}
                    onClose={() => setGoal(null)}
                    onFinished={(updated) =>
                        setTimeout(() => {
                            if (updated) {
                                replace(updated);
                            }

                            setGoal(null);
                        }, FINISH_DELAY_MS)
                    }
                />
            )}
        </>
    );
}
