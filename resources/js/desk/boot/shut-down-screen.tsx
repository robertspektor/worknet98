import { useTranslation } from '@/i18n/use-translation';

export function ShutDownScreen() {
    const { t } = useTranslation();

    return (
        <div className="safe-to-turn-off">
            {t('shut_down.safe')}
            <small>{t('shut_down.note')}</small>
        </div>
    );
}
