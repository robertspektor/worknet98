import { useTranslation } from '@/i18n/use-translation';
import { show as rankingsShow } from '@/routes/api/v1/rankings';
import type { Rankings } from '@/types';
import { useApiResource } from '../../api/use-api-resource';
import { AppLoading } from '../../ui/app-loading';

export function RankingsTab() {
    const { t } = useTranslation();
    const { data } = useApiResource<Rankings>(rankingsShow.url());

    if (!data) {
        return <AppLoading />;
    }

    const board = (title: string, entries: Rankings['branch']) => (
        <section className="forum-ranking">
            <h4 className="forum-ranking-title">{title}</h4>
            {entries.length === 0 ? (
                <p className="muted">{t('forum.rankings.empty')}</p>
            ) : (
                <ol className="forum-ranking-list">
                    {entries.map((entry) => (
                        <li
                            key={entry.employment_id}
                            className={entry.is_own ? 'is-own' : ''}
                        >
                            <span>
                                {entry.name} &middot; {entry.company}
                            </span>
                            <span>
                                {t('forum.rankings.score')}: {entry.score}
                                {entry.excellent_reviews > 0 &&
                                    ` (${t('forum.rankings.excellent', { count: entry.excellent_reviews })})`}
                            </span>
                        </li>
                    ))}
                </ol>
            )}
        </section>
    );

    return (
        <div className="forum-rankings sunken">
            {data.awards.length > 0 && (
                <section className="forum-ranking">
                    <h4 className="forum-ranking-title">
                        {t('forum.rankings.awards')}
                    </h4>
                    <ul className="forum-ranking-list">
                        {data.awards.map((award) => (
                            <li
                                key={award.period}
                                className={award.is_own ? 'is-own' : ''}
                            >
                                <span>
                                    {award.period}: {award.name}
                                </span>
                                <span>{award.title}</span>
                            </li>
                        ))}
                    </ul>
                </section>
            )}
            {board(t('forum.rankings.branch'), data.branch)}
            {board(t('forum.rankings.world'), data.world)}
        </div>
    );
}
