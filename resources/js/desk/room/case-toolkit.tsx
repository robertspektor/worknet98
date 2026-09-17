import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import type { SwapGoal } from '../hardware/cpu-swap-state';
import { useHomeComputer } from '../hardware/home-computer-provider';
import { TowerWorkbench } from './workbench/tower-workbench';

const FINISH_DELAY_MS = 1800;

export function CaseToolkit() {
    const { t } = useTranslation();
    const { homeComputer, replace } = useHomeComputer();
    const [goal, setGoal] = useState<SwapGoal | null>(null);
    const newCpu =
        homeComputer?.desk_parts.find((part) => !part.is_used) ?? null;
    const availableGoal: SwapGoal | null = newCpu
        ? 'swap'
        : homeComputer?.cpu.needs_thermal_paste
          ? 'repaste'
          : null;

    if (availableGoal === null && goal === null) {
        return null;
    }

    return (
        <>
            {availableGoal && (
                <button
                    type="button"
                    className="desk-item case-toolkit"
                    title={t('workbench.open')}
                    onClick={() => setGoal(availableGoal)}
                >
                    <span className="screwdriver" aria-hidden="true">
                        <span className="screwdriver-handle" />
                        <span className="screwdriver-shaft" />
                    </span>
                    <span className="desk-item-caption">
                        {t('workbench.open')}
                    </span>
                </button>
            )}
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
