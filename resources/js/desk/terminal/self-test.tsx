import { useTranslation } from '@/i18n/use-translation';
import { useTimedSteps } from '../boot/use-timed-steps';

const LINE_DELAY_MS = 260;

const LINES = [
    'terminal.boot_brand',
    'terminal.boot_firmware',
    null,
    'terminal.boot_selftest',
    'terminal.boot_card',
    'terminal.boot_modem',
];

export function SelfTest({ onDone }: { onDone: () => void }) {
    const { t } = useTranslation();
    const visibleLines = useTimedSteps(LINES.length, LINE_DELAY_MS, onDone);

    return (
        <div className="bios">
            <pre className="bios-text">
                {LINES.slice(0, visibleLines)
                    .map((key) => (key === null ? '' : t(key)))
                    .join('\n')}
            </pre>
        </div>
    );
}
