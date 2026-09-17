import { useTranslation } from '@/i18n/use-translation';
import type { SiteProps } from './site-registry';

export function StartPage({ onOpen }: SiteProps) {
    const { t } = useTranslation();

    return (
        <div className="web-page start-page">
            <h1 className="start-page-title">{t('browser.start.title')}</h1>
            <p>{t('browser.start.intro')}</p>
            <h2 className="start-page-heading">{t('browser.start.links')}</h2>
            <ul className="start-page-links">
                <li>
                    <button
                        type="button"
                        className="web-link"
                        onClick={() => onOpen('diskdepot')}
                    >
                        {t('browser.start.diskdepot')}
                    </button>
                </li>
            </ul>
            <p className="start-page-counter">
                {t('browser.start.counter', { count: '000451' })}
            </p>
        </div>
    );
}
