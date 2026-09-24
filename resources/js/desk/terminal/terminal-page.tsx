import type { ReactNode } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { formatGameStamp } from '../clock/game-clock';
import { useGameTime } from '../clock/use-game-time';
import { LocaleSelect } from '../ui/locale-select';
import { useLocaleSwitch } from '../ui/use-locale-switch';
import { TERMINAL_ID } from './terminal-id';

/* Every mask of the city network looks the same: the name of the net over a
   rule, the business in the middle, date and time in the corner. */

export function TerminalPage({
    city,
    children,
}: {
    city: string | null;
    children: ReactNode;
}) {
    const { t, locale } = useTranslation();
    const switchLocale = useLocaleSwitch();
    const stamp = formatGameStamp(useGameTime(), locale);

    return (
        <div className="access">
            <h1 className="access-net">
                {city === null
                    ? t('terminal.access', { id: TERMINAL_ID })
                    : t('terminal.network', { city: city.toUpperCase() })}
            </h1>
            <div className="access-main">{children}</div>
            <footer className="access-foot">
                <label className="access-language" htmlFor="access-locale">
                    <span>{t('terminal.language')}</span>
                    <LocaleSelect
                        id="access-locale"
                        value={locale}
                        onChange={switchLocale}
                    />
                </label>
                <span className="access-stamp">{stamp}</span>
            </footer>
        </div>
    );
}
