import { useTranslation } from '@/i18n/use-translation';
import { NetworkHeading } from './network-heading';

export function DisconnectScreen({ city }: { city: string | null }) {
    const { t } = useTranslation();

    return (
        <div className="access is-connecting">
            <NetworkHeading city={city} />
            <p className="access-progress">
                {t('terminal.disconnecting')}
                <span className="access-dots" aria-hidden="true" />
            </p>
        </div>
    );
}
