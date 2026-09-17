import { Link } from '@inertiajs/react';
import { useTranslation } from '@/i18n/use-translation';
import { home } from '@/routes';
import { usePlaceable } from '../placement/use-placeable';

export function HouseKeys() {
    const { t } = useTranslation();
    const { className, ...placeable } =
        usePlaceable<HTMLAnchorElement>('house-keys');

    return (
        <Link
            href={home.url()}
            className={`desk-item house-keys ${className}`}
            {...placeable}
        >
            <svg viewBox="0 0 64 48" aria-hidden="true">
                <circle cx="20" cy="16" r="11" className="key-ring" />
                <g className="key">
                    <circle cx="20" cy="30" r="6" />
                    <path d="M25 29h26v4h-3v4h-3v-4h-3v5h-3v-5H25z" />
                </g>
                <g className="key key-small">
                    <circle cx="11" cy="26" r="5" />
                    <path d="M9 30h4v14l-2 3-2-3v-2h-2v-3h2z" />
                </g>
            </svg>
            <span className="key-tag">{t('commute.go_home')}</span>
        </Link>
    );
}
