import { Head, router, usePoll } from '@inertiajs/react';
import { useTranslation } from '@/i18n/use-translation';
import type { CityOverview, WorldEventEntry } from '@/types';
import { formatAmount, formatDateTime } from '@/desk/ui/format';

const POLL_INTERVAL_MS = 30_000;

export default function Citynet({
    mayor,
    cities,
    events,
}: {
    mayor: string;
    cities: CityOverview[];
    events: WorldEventEntry[];
}) {
    const { t, locale } = useTranslation();
    usePoll(POLL_INTERVAL_MS);

    return (
        <>
            <Head title="CITYNET" />
            <div className="citynet">
                <header className="citynet-header">
                    <h1 className="citynet-title">{t('citynet.title')}</h1>
                    <span>{t('citynet.mayor', { name: mayor })}</span>
                </header>
                <div className="citynet-cities">
                    {cities.map((city) => (
                        <section key={city.name} className="citynet-city">
                            <h2>{city.name.toUpperCase()}</h2>
                            <dl className="citynet-figures">
                                <dt>{t('citynet.population')}</dt>
                                <dd>{formatAmount(city.residents, locale)}</dd>
                                <dt>{t('citynet.households')}</dt>
                                <dd>{formatAmount(city.households, locale)}</dd>
                                <dt>{t('citynet.employed')}</dt>
                                <dd>{formatAmount(city.employed, locale)}</dd>
                                <dt>{t('citynet.unemployment')}</dt>
                                <dd>
                                    {formatAmount(
                                        city.unemployment_rate,
                                        locale,
                                    )}{' '}
                                    %
                                </dd>
                                <dt>{t('citynet.average_income')}</dt>
                                <dd>
                                    {formatAmount(city.average_income, locale)}{' '}
                                    C
                                </dd>
                                <dt>{t('citynet.organizations')}</dt>
                                <dd>
                                    {formatAmount(city.organizations, locale)}
                                </dd>
                                <dt>{t('citynet.open_positions')}</dt>
                                <dd>
                                    {formatAmount(city.open_positions, locale)}
                                </dd>
                            </dl>
                        </section>
                    ))}
                </div>
                <h2 className="citynet-section-title">{t('citynet.events')}</h2>
                {events.length === 0 ? (
                    <p className="citynet-empty">{t('citynet.no_events')}</p>
                ) : (
                    <table className="citynet-events">
                        <thead>
                            <tr>
                                <th>{t('citynet.event.time')}</th>
                                <th>{t('citynet.event.city')}</th>
                                <th>{t('citynet.event.type')}</th>
                                <th>{t('citynet.event.person')}</th>
                                <th>{t('citynet.event.handled_by')}</th>
                                <th>{t('citynet.event.caused_by')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {events.map((event) => (
                                <tr key={event.id}>
                                    <td>
                                        {formatDateTime(
                                            event.occurred_at,
                                            locale,
                                        )}
                                    </td>
                                    <td>{event.city}</td>
                                    <td>{t(`citynet.types.${event.type}`)}</td>
                                    <td>{event.person}</td>
                                    <td className="is-dim">
                                        {event.handled_by ?? '-'}
                                    </td>
                                    <td className="is-dim">
                                        {event.caused_by
                                            ? t(
                                                  `citynet.types.${event.caused_by}`,
                                              )
                                            : '-'}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                )}
                <footer className="citynet-footer">
                    <button
                        type="button"
                        className="citynet-refresh"
                        onClick={() => router.reload()}
                    >
                        [ {t('citynet.refresh')} ]
                    </button>
                    <p>{t('citynet.footer')}</p>
                </footer>
            </div>
        </>
    );
}
