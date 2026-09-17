import { useTranslation } from '@/i18n/use-translation';
import type { DeskPart } from '@/types';
import { useHomeComputer } from '../hardware/home-computer-provider';
import { CpuChipArt } from './cpu-chip-art';

function DeskPartItem({ part }: { part: DeskPart }) {
    const { t } = useTranslation();
    const name = t(`hardware_part.${part.slug}.label`);
    const state = part.is_used ? 'used' : 'new';

    return (
        <div
            className={`desk-part is-${state}`}
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

export function DeskParts() {
    const { homeComputer } = useHomeComputer();
    const parts = homeComputer?.desk_parts ?? [];

    if (parts.length === 0) {
        return null;
    }

    return (
        <div className="desk-item desk-parts">
            {parts.map((part) => (
                <DeskPartItem key={part.id} part={part} />
            ))}
        </div>
    );
}
