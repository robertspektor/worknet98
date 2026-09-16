import { useTranslation } from '@/i18n/use-translation';
import { useEdition } from '../computer/edition-context';
import { PixelIcon } from '../ui/pixel-icon';
import { useTimedSteps } from './use-timed-steps';

const STATUS_DELAY_MS = 650;

export function SplashScreen({ onDone }: { onDone: () => void }) {
    const { t } = useTranslation();
    const { subtitleKey, bootStatusKeys } = useEdition();
    const step = useTimedSteps(bootStatusKeys.length, STATUS_DELAY_MS, onDone);

    return (
        <div className="splash">
            <div className="splash-brand">
                <PixelIcon name="logo" size={96} />
                <div className="splash-title">
                    Desk<b>OS</b>
                    <sup>98</sup>
                </div>
                <div className="splash-subtitle">{t(subtitleKey)}</div>
            </div>
            <div className="splash-status">{t(bootStatusKeys[step - 1])}</div>
            <div className="splash-bar">
                <span />
            </div>
        </div>
    );
}
