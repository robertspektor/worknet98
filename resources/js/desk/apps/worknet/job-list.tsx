import { useTranslation } from '@/i18n/use-translation';
import type { JobApplication, JobOpening } from '@/types';
import { formatAmount } from '../../ui/format';
import { applicationFor } from './job-board-state';
import { JobStatusBadge } from './job-status-badge';

export function JobList({
    openings,
    applications,
    onSelect,
}: {
    openings: JobOpening[];
    applications: JobApplication[];
    onSelect: (opening: JobOpening) => void;
}) {
    const { t, locale } = useTranslation();

    return (
        <section>
            <h2 className="worknet-heading">{t('worknet.open_positions')}</h2>
            {openings.length === 0 ? (
                <p>{t('worknet.empty')}</p>
            ) : (
                <table className="job-table">
                    <thead>
                        <tr>
                            <th>{t('worknet.column_position')}</th>
                            <th>{t('worknet.column_company')}</th>
                            <th>{t('worknet.column_salary')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {openings.map((opening) => (
                            <tr key={opening.id}>
                                <td>
                                    <button
                                        type="button"
                                        className="worknet-link"
                                        onClick={() => onSelect(opening)}
                                    >
                                        {opening.title}
                                    </button>
                                    <JobStatusBadge
                                        application={applicationFor(
                                            applications,
                                            opening.id,
                                        )}
                                    />
                                </td>
                                <td className="job-company-cell">
                                    {opening.company.name}
                                </td>
                                <td className="job-salary">
                                    {t('money.per_day', {
                                        amount: formatAmount(
                                            opening.daily_salary,
                                            locale,
                                        ),
                                    })}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            )}
        </section>
    );
}
