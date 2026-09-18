import type { CSSProperties } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import type { WeeklyGoal } from '@/types';
import { goalProgress } from '../../shift/work-time';
import { formatAmount } from '../../ui/format';

export function WeeklyGoalSheet({ goal }: { goal: WeeklyGoal }) {
    const { t, locale } = useTranslation();
    const progress = goalProgress(goal.resolved_cases, goal.target);

    return (
        <section className="work-time-sheet">
            <h3 className="work-time-heading">{t('time_clock.week')}</h3>
            <div
                className="work-time-bar sunken"
                role="progressbar"
                aria-valuenow={Math.round(progress * 100)}
                aria-valuemin={0}
                aria-valuemax={100}
            >
                <span
                    className="work-time-fill"
                    style={
                        { '--progress': `${progress * 100}%` } as CSSProperties
                    }
                />
            </div>
            <dl className="work-time-rows">
                <dt>{t('time_clock.week_cases')}</dt>
                <dd>
                    {t('time_clock.week_cases_value', {
                        done: goal.resolved_cases,
                        target: goal.target,
                    })}
                </dd>
                <dt>{t('time_clock.week_bonus')}</dt>
                <dd className={goal.achieved ? 'is-achieved' : undefined}>
                    {t(
                        goal.achieved
                            ? 'time_clock.week_bonus_paid'
                            : 'time_clock.week_bonus_value',
                        { bonus: formatAmount(goal.bonus, locale) },
                    )}
                </dd>
            </dl>
        </section>
    );
}
