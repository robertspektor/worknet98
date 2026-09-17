import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { useEdition } from '../../computer/edition-context';
import { pageLoadDurationMs } from '../../hardware/performance';
import {
    canGoBack,
    currentPage,
    goBack,
    startAt,
    visit,
} from './browser-history';
import type { BrowserHistory } from './browser-history';
import { isPageLoaded } from './page-load';
import { SITES } from './sites/site-registry';
import type { SiteId } from './sites/site-registry';
import { usePageLoad } from './use-page-load';

export function BrowserApp() {
    const { t } = useTranslation();
    const { cpu } = useEdition();
    const [history, setHistory] = useState(() => startAt<SiteId>('start'));
    const [visitKey, setVisitKey] = useState(0);
    const progress = usePageLoad(visitKey, pageLoadDurationMs(cpu.speedMhz));
    const site = SITES[currentPage(history)];
    const Site = site.component;

    const navigate = (next: BrowserHistory<SiteId>) => {
        setHistory(next);
        setVisitKey((key) => key + 1);
    };

    return (
        <div className="browser">
            <div className="browser-toolbar">
                <button
                    type="button"
                    className="button browser-button"
                    disabled={!canGoBack(history)}
                    onClick={() => navigate(goBack(history))}
                >
                    {t('browser.back')}
                </button>
                <button
                    type="button"
                    className="button browser-button"
                    onClick={() => navigate(visit(history, 'start'))}
                >
                    {t('browser.home')}
                </button>
                <label className="browser-address">
                    <span>{t('browser.address')}</span>
                    <input className="input" value={site.address} readOnly />
                </label>
            </div>
            <div className="browser-viewport">
                {isPageLoaded(progress) && (
                    <Site onOpen={(next) => navigate(visit(history, next))} />
                )}
            </div>
            <div className="browser-status">
                <span className="browser-status-text sunken">
                    {isPageLoaded(progress)
                        ? t('browser.done')
                        : t('browser.loading', { address: site.address })}
                </span>
                <span className="browser-progress sunken">
                    <span
                        className="browser-progress-bar"
                        style={{ width: `${Math.round(progress * 100)}%` }}
                    />
                </span>
            </div>
        </div>
    );
}
