import { useTranslation } from '@/i18n/use-translation';
import type { Milestone } from '@/types';
import { formatDate } from '../../ui/format';

export function MilestoneRow({ milestone }: { milestone: Milestone }) {
    const { t, locale } = useTranslation();

    return (
        <li
            className={`personal-record-entry ${milestone.achieved ? 'is-achieved' : ''}`}
        >
            <span className="personal-record-mark" aria-hidden="true">
                {milestone.achieved ? '✓' : '·'}
            </span>
            <span className="personal-record-body">
                <span className="personal-record-name">
                    {t(`milestone.${milestone.key}.title`)}
                </span>
                <span className="personal-record-note muted">
                    {milestone.achieved_at === null
                        ? t(`milestone.${milestone.key}.hint`)
                        : t('personal_record.achieved_at', {
                              date: formatDate(milestone.achieved_at, locale),
                          })}
                </span>
            </span>
        </li>
    );
}
