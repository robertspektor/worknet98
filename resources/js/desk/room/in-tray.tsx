import { useTranslation } from '@/i18n/use-translation';
import { usePlaceable } from '../placement/use-placeable';

export function InTray() {
    const { className, ...placeable } = usePlaceable<HTMLDivElement>('in-tray');
    const { t } = useTranslation();

    return (
        <div
            className={`desk-item in-tray ${className}`}
            {...placeable}
            aria-hidden="true"
        >
            <span className="tray-paper" />
            <span className="tray-paper" />
            <span className="tray-front">{t('office.in_tray')}</span>
        </div>
    );
}
