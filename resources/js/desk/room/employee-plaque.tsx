import { useTranslation } from '@/i18n/use-translation';
import { usePlaceable } from '../placement/use-placeable';

export function EmployeePlaque({ period }: { period: string }) {
    const { t } = useTranslation();
    const { className, ...placeable } =
        usePlaceable<HTMLDivElement>('employee-plaque');

    return (
        <div
            className={`desk-item employee-plaque ${className}`}
            title={t('office.plaque.hint')}
            {...placeable}
        >
            <span className="employee-plaque-face">
                <span className="employee-plaque-star">★</span>
                {t('office.plaque.title')}
                <span className="employee-plaque-period">{period}</span>
            </span>
        </div>
    );
}
