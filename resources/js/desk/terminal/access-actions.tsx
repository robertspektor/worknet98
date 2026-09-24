import { useTranslation } from '@/i18n/use-translation';

export function AccessActions({
    isBusy,
    onBack,
}: {
    isBusy: boolean;
    onBack: () => void;
}) {
    const { t } = useTranslation();

    return (
        <div className="access-actions">
            <button type="button" className="access-button" onClick={onBack}>
                {t('terminal.back')}
            </button>
            <button type="submit" className="access-button" disabled={isBusy}>
                {t('terminal.submit')}
            </button>
        </div>
    );
}
