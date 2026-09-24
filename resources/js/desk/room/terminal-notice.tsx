import { useTranslation } from '@/i18n/use-translation';
import { TERMINAL_ID } from '../terminal/terminal-id';

/* The plate the city screws to the wall above every terminal: what net this
   is, which terminal it is and who may use it. */

export function TerminalNotice({ city }: { city: string | null }) {
    const { t } = useTranslation();

    return (
        <aside className="terminal-notice">
            {city !== null && (
                <span className="notice-net">
                    {t('terminal.network', { city: city.toUpperCase() })}
                </span>
            )}
            <span className="notice-id">
                {t('terminal.notice_terminal', { id: TERMINAL_ID })}
            </span>
            <span className="notice-access">{t('terminal.notice_access')}</span>
        </aside>
    );
}
