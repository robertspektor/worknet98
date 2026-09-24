import { useTranslation } from '@/i18n/use-translation';
import type { DeskPart } from '@/types';
import { useHomeComputer } from '../hardware/home-computer-provider';
import { useWorkbench } from '../hardware/workbench-provider';
import { usePlaceable } from '../placement/use-placeable';
import { CpuChipArt } from './cpu-chip-art';

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

/* What lies next to the machine while there is work on it: the processor
   that came in the post and the screwdriver that opens the case. */

export function DeskWorkshop() {
    const { homeComputer } = useHomeComputer();
    const { job, open } = useWorkbench();
    const parts = homeComputer?.desk_parts ?? [];

    if (parts.length === 0 && job === null) {
        return null;
    }

    return (
        <div className="desk-item desk-workshop">
            {job !== null && <Screwdriver onPickUp={open} />}
            {parts.map((part) => (
                <DeskPartItem key={part.id} part={part} />
            ))}
        </div>
    );
}
