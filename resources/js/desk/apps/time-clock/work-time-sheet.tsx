import type { CSSProperties } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import type { ShiftStatus } from '@/types';
import { formatWorkTime, summarizeWorkTime } from '../../shift/work-time';
import { formatAmount } from '../../ui/format';

function contractMonth(startsOn: string, locale: string): string {
    return new Date(`${startsOn}T00:00:00`).toLocaleDateString(locale, {
        month: 'long',
        year: 'numeric',
    });
}

export function WorkTimeSheet({ shift }: { shift: ShiftStatus }) {
    const { t, locale } = useTranslation();
    const time = summarizeWorkTime(shift.worked_seconds, shift.target_seconds);
    const hours = (seconds: number) =>
        t('time_clock.hours', { time: formatWorkTime(seconds) });
    const isOvertime = time.overtimeSeconds > 0;

    return (
        <section className="work-time-sheet">
            <h3 className="work-time-heading">
                {t('time_clock.period', {
                    month: contractMonth(shift.period.starts_on, locale),
                })}
            </h3>
            <div
                className="work-time-bar sunken"
                role="progressbar"
                aria-valuenow={Math.round(time.progress * 100)}
                aria-valuemin={0}
                aria-valuemax={100}
            >
                <span
                    className="work-time-fill"
                    style={
                        {
                            '--progress': `${time.progress * 100}%`,
                        } as CSSProperties
                    }
                />
            </div>
            <dl className="work-time-rows">
                <dt>{t('time_clock.worked')}</dt>
                <dd>{hours(time.workedSeconds)}</dd>
                <dt>{t('time_clock.target')}</dt>
                <dd>{hours(time.targetSeconds)}</dd>
                <dt>{t('time_clock.salary')}</dt>
                <dd>
                    {t('time_clock.salary_value', {
                        earned: formatAmount(shift.earned_salary, locale),
                        full: formatAmount(shift.full_salary, locale),
                    })}
                </dd>
                <dt>
                    {t(
                        isOvertime
                            ? 'time_clock.overtime'
                            : 'time_clock.remaining',
                    )}
                </dt>
                <dd className={isOvertime ? 'is-overtime' : undefined}>
                    {isOvertime
                        ? `+${hours(time.overtimeSeconds)}`
                        : hours(time.remainingSeconds)}
                </dd>
            </dl>
        </section>
    );
}
