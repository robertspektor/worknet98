import { useTranslation } from '@/i18n/use-translation';

export function ThermalFaultScreen() {
    const { t } = useTranslation();

    return (
        <div className="thermal-fault">
            <p className="thermal-fault-title">{t('thermal_fault.title')}</p>
            <p>{t('thermal_fault.message')}</p>
            <p>{t('thermal_fault.hint')}</p>
            <p className="thermal-fault-prompt">{t('thermal_fault.prompt')}</p>
        </div>
    );
}
