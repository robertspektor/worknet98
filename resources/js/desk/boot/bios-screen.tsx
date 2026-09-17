import { useTranslation } from '@/i18n/use-translation';
import { useEdition } from '../computer/edition-context';
import {
    biosLineDelayMs,
    memoryCountDurationMs,
} from '../hardware/performance';
import { INSTALLED_MEMORY_KB } from './memory-count';
import { useMemoryCount } from './use-memory-count';
import { useTimedSteps } from './use-timed-steps';

const LINES = [
    'bios.line_1',
    'bios.line_2',
    null,
    'bios.line_3',
    'bios.line_4',
    null,
    'bios.line_5',
    'bios.line_6',
    'bios.line_7',
    null,
    'bios.line_8',
];
const MEMORY_LINE = LINES.indexOf('bios.line_4') + 1;

export function BiosScreen({ onDone }: { onDone: () => void }) {
    const { t } = useTranslation();
    const { cpu } = useEdition();
    const countDurationMs = memoryCountDurationMs(cpu.speedMhz);
    const visibleLines = useTimedSteps(
        LINES.length,
        (step) =>
            step === MEMORY_LINE
                ? countDurationMs
                : biosLineDelayMs(cpu.speedMhz),
        onDone,
    );
    const memoryKb = useMemoryCount(
        visibleLines >= MEMORY_LINE,
        countDurationMs,
    );
    const memoryResult =
        memoryKb === INSTALLED_MEMORY_KB ? ` ${t('bios.memory_ok')}` : '';

    const lineText = (key: string | null) => {
        switch (key) {
            case null:
                return '';
            case 'bios.line_3':
                return t(key, { cpu: t(`hardware_part.${cpu.slug}.label`) });
            case 'bios.line_4':
                return t(key, { memory: `${memoryKb}K${memoryResult}` });
            default:
                return t(key);
        }
    };

    return (
        <div className="bios">
            <pre className="bios-text">
                {LINES.slice(0, visibleLines).map(lineText).join('\n')}
            </pre>
        </div>
    );
}
