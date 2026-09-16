import { useTranslation } from '@/i18n/use-translation';

export function InTray() {
    const { t } = useTranslation();

    return (
        <div className="desk-item in-tray" aria-hidden="true">
            <span className="tray-paper" />
            <span className="tray-paper" />
            <span className="tray-front">{t('office.in_tray')}</span>
        </div>
    );
}
