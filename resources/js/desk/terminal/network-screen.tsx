import { useTranslation } from '@/i18n/use-translation';
import { useTimedSteps } from '../boot/use-timed-steps';
import { NetworkHeading } from './network-heading';

const CONNECT_MS = 1600;

export function NetworkScreen({
    city,
    onDone,
}: {
    city: string | null;
    onDone: () => void;
}) {
    const { t } = useTranslation();
    useTimedSteps(1, CONNECT_MS, onDone);

    return (
        <div className="access is-connecting">
            <NetworkHeading city={city} />
            <p className="access-progress">
                {t('terminal.connecting')}
                <span className="access-dots" aria-hidden="true" />
            </p>
        </div>
    );
}
