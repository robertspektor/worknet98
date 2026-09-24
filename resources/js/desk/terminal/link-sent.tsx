import { useTranslation } from '@/i18n/use-translation';
import { TerminalPage } from './terminal-page';
import { useHotkeys } from './use-hotkeys';

export function LinkSent({
    city,
    onBack,
}: {
    city: string | null;
    onBack: () => void;
}) {
    const { t } = useTranslation();
    useHotkeys({ Escape: onBack });

    return (
        <TerminalPage city={city}>
            <h2 className="access-heading-line">{t('terminal.sent_title')}</h2>
            <p className="access-lead">{t('terminal.sent_body')}</p>
            <div className="access-actions">
                <button
                    type="button"
                    className="access-button"
                    onClick={onBack}
                >
                    {t('terminal.try_again')}
                </button>
            </div>
        </TerminalPage>
    );
}
