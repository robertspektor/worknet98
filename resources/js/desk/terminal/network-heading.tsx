import { useTranslation } from '@/i18n/use-translation';
import { TERMINAL_ID } from './terminal-id';

export function NetworkHeading({ city }: { city: string | null }) {
    const { t } = useTranslation();

    return (
        <header className="access-heading">
            {city !== null && (
                <span className="access-network">
                    {t('terminal.network', { city: city.toUpperCase() })}
                </span>
            )}
            <span className="access-kind">
                {t('terminal.access', { id: TERMINAL_ID })}
            </span>
        </header>
    );
}
