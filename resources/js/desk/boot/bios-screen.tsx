import { useTranslation } from '@/i18n/use-translation';
import { useTimedSteps } from './use-timed-steps';

const LINE_KEYS = [
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
const LINE_DELAY_MS = 150;

export function BiosScreen({ onDone }: { onDone: () => void }) {
    const { t } = useTranslation();
    const visibleLines = useTimedSteps(LINE_KEYS.length, LINE_DELAY_MS, onDone);

    return (
        <div className="bios">
            <pre className="bios-text">
                {LINE_KEYS.slice(0, visibleLines)
                    .map((key) => (key ? t(key) : ''))
                    .join('\n')}
            </pre>
        </div>
    );
}
