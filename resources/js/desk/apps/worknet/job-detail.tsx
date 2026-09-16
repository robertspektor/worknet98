import { useTranslation } from '@/i18n/use-translation';
import type { JobApplication, JobOpening } from '@/types';
import { formatAmount } from '../../ui/format';
import { PixelIcon } from '../../ui/pixel-icon';
import { ApplicationForm } from './application-form';

export function JobDetail({
    opening,
    application,
    onBack,
    onApply,
}: {
    opening: JobOpening;
    application: JobApplication | null;
    onBack: () => void;
    onApply: (message: string) => Promise<void>;
}) {
    const { t, locale } = useTranslation();

    return (
        <article className="job-detail">
            <button type="button" className="worknet-link" onClick={onBack}>
                &laquo; {t('worknet.back')}
            </button>
            <h2 className="worknet-heading">{opening.title}</h2>
            <p className="job-company">
                <b>{opening.company.name}</b> &middot;{' '}
                {opening.company.industry} &middot;{' '}
                {t('money.per_day', {
                    amount: formatAmount(opening.daily_salary, locale),
                })}
            </p>
            <p className="job-tagline">&quot;{opening.company.tagline}&quot;</p>
            <h3 className="worknet-subheading">
                {t('worknet.about_position')}
            </h3>
            <p>{opening.description}</p>
            <h3 className="worknet-subheading">{t('worknet.about_company')}</h3>
            <p>{opening.company.description}</p>
            {application ? (
                <div className="notice">
                    <PixelIcon
                        name={
                            application.status === 'accepted' ? 'mail' : 'info'
                        }
                    />
                    <p>{t(`worknet.status_detail.${application.status}`)}</p>
                </div>
            ) : (
                <ApplicationForm onSubmit={onApply} />
            )}
        </article>
    );
}
