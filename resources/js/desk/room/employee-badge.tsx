import { Link } from '@inertiajs/react';
import { useTranslation } from '@/i18n/use-translation';
import { office } from '@/routes';
import { usePlaceable } from '../placement/use-placeable';

export function EmployeeBadge({ company }: { company: string }) {
    const { t } = useTranslation();
    const { className, ...placeable } =
        usePlaceable<HTMLAnchorElement>('employee-badge');

    return (
        <Link
            href={office.url()}
            className={`desk-item employee-badge ${className}`}
            {...placeable}
        >
            <span className="lanyard" aria-hidden="true" />
            <span className="badge-card">
                <span className="badge-photo" aria-hidden="true" />
                <span className="badge-company">{company}</span>
                <span className="badge-barcode" aria-hidden="true" />
            </span>
            <span className="desk-item-caption">{t('commute.go_to_work')}</span>
        </Link>
    );
}
