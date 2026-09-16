import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import {
    canGoBack,
    currentPage,
    goBack,
    startAt,
    visit,
} from './browser-history';
import { DiskDepotSite } from './sites/disk-depot-site';
import { StartPage } from './sites/start-page';

type Site = 'start' | 'diskdepot';

const ADDRESSES: Record<Site, string> = {
    start: 'http://www.welcome.wn/',
    diskdepot: 'http://www.diskdepot.wn/',
};

export function BrowserApp() {
    const { t } = useTranslation();
    const [history, setHistory] = useState(() => startAt<Site>('start'));
    const site = currentPage(history);
    const open = (next: Site) => setHistory(visit(history, next));

    return (
        <div className="browser">
            <div className="browser-toolbar">
                <button
                    type="button"
                    className="button browser-button"
                    disabled={!canGoBack(history)}
                    onClick={() => setHistory(goBack(history))}
                >
                    {t('browser.back')}
                </button>
                <button
                    type="button"
                    className="button browser-button"
                    onClick={() => open('start')}
                >
                    {t('browser.home')}
                </button>
                <label className="browser-address">
                    <span>{t('browser.address')}</span>
                    <input className="input" value={ADDRESSES[site]} readOnly />
                </label>
            </div>
            <div className="browser-viewport">
                {site === 'start' ? (
                    <StartPage onOpenDiskDepot={() => open('diskdepot')} />
                ) : (
                    <DiskDepotSite />
                )}
            </div>
        </div>
    );
}
