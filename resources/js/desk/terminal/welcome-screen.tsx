import { useTranslation } from '@/i18n/use-translation';
import { TerminalPage } from './terminal-page';

export function WelcomeScreen({
    address,
    city,
    isNewResident,
    onLeave,
}: {
    address: string;
    city: string | null;
    isNewResident: boolean;
    onLeave: () => void;
}) {
    const { t } = useTranslation();

    return (
        <TerminalPage city={city}>
            <h2 className="access-heading-line">
                {t(
                    isNewResident
                        ? 'terminal.registered_title'
                        : 'terminal.identity_title',
                )}
            </h2>
            {isNewResident && (
                <>
                    <p className="access-lead">
                        {t('terminal.registered_body')}
                    </p>
                    <p className="access-lead">
                        {t('terminal.registered_note')}
                    </p>
                </>
            )}

            <dl className="access-card">
                <dt>{t('terminal.resident_address')}</dt>
                <dd>{address}</dd>
                {city !== null && (
                    <>
                        <dt>{t('terminal.resident_city')}</dt>
                        <dd>{city}</dd>
                    </>
                )}
            </dl>

            <div className="access-actions access-actions-end">
                <button
                    type="button"
                    className="access-button"
                    onClick={onLeave}
                    autoFocus
                >
                    {t('terminal.home')}
                </button>
            </div>
        </TerminalPage>
    );
}
