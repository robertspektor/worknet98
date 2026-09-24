import { useTranslation } from '@/i18n/use-translation';
import type { CityFigures } from '@/types';
import { TerminalPage } from './terminal-page';
import { useHotkeys } from './use-hotkeys';

const LABELS: Record<keyof CityFigures, string> = {
    residents: 'landing.city_residents',
    companies: 'landing.city_companies',
    open_positions: 'landing.city_jobs',
    deliveries: 'landing.city_deliveries',
};

export function InfoScreen({
    city,
    figures,
    onBack,
}: {
    city: string | null;
    figures: CityFigures | null;
    onBack: () => void;
}) {
    const { t, locale } = useTranslation();
    useHotkeys({ Escape: onBack, F4: onBack });

    return (
        <TerminalPage city={city}>
            <h2 className="access-heading-line">
                {t('terminal.info_title', { city: (city ?? '').toUpperCase() })}
            </h2>

            {figures === null ? (
                <p className="access-welcome">{t('terminal.info_empty')}</p>
            ) : (
                <dl className="access-figures">
                    {Object.entries(LABELS).map(([figure, label]) => (
                        <div key={figure}>
                            <dt>{t(label)}</dt>
                            <dd>
                                {figures[
                                    figure as keyof CityFigures
                                ].toLocaleString(locale)}
                            </dd>
                        </div>
                    ))}
                </dl>
            )}

            <p className="access-note">{t('terminal.info_note')}</p>
            <button type="button" className="access-entry" onClick={onBack}>
                <span className="entry-key">[ESC]</span>
                <span className="entry-text">
                    <span className="entry-action">{t('terminal.back')}</span>
                </span>
            </button>
        </TerminalPage>
    );
}
