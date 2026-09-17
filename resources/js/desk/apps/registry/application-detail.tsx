import { useTranslation } from '@/i18n/use-translation';
import type { ApplicationDecision, CivilApplication } from '@/types';
import { formatDay } from '../../ui/format';
import { applicationStatus, decidedByText } from './application-status';

export function ApplicationDetail({
    application,
    isBusy,
    onDecide,
}: {
    application: CivilApplication | null;
    isBusy: boolean;
    onDecide: (decision: ApplicationDecision) => void;
}) {
    const { t, locale } = useTranslation();

    if (!application) {
        return (
            <div className="records-detail sunken">
                <p className="muted">{t('registry.select_application')}</p>
            </div>
        );
    }

    const decidedBy = decidedByText(application, t);
    const canDecide = application.is_own && !application.decision;

    return (
        <div className="records-detail sunken registry-detail">
            <h3 className="records-detail-title">
                {t(`registry.kinds.${application.kind}`)}:{' '}
                {application.applicant}
            </h3>
            <dl className="property-list">
                <dt>{t('registry.applicant')}</dt>
                <dd>{application.applicant}</dd>
                <dt>{t('registry.claimed_address')}</dt>
                <dd>
                    {application.claimed_street}
                    <br />
                    {application.claimed_district}
                </dd>
                {application.partner && (
                    <>
                        <dt>{t('registry.partner')}</dt>
                        <dd>
                            {application.partner}
                            <br />
                            {application.claimed_partner_street},{' '}
                            {application.claimed_partner_district}
                        </dd>
                    </>
                )}
                {application.detail && (
                    <>
                        <dt>{t(`registry.details.${application.kind}`)}</dt>
                        <dd>{application.detail}</dd>
                    </>
                )}
                {application.new_street && (
                    <>
                        <dt>{t('registry.new_address')}</dt>
                        <dd>
                            {application.new_street}
                            <br />
                            {application.new_district}
                        </dd>
                    </>
                )}
                <dt>{t('registry.moved_on')}</dt>
                <dd>{formatDay(application.moved_on, locale)}</dd>
                <dt>{t('registry.status')}</dt>
                <dd>
                    {t(`registry.statuses.${applicationStatus(application)}`)}
                    {decidedBy && (
                        <>
                            <br />
                            <span className="muted">{decidedBy}</span>
                        </>
                    )}
                </dd>
            </dl>
            {canDecide && (
                <div className="app-actions app-actions-end">
                    <button
                        type="button"
                        className="button"
                        disabled={isBusy}
                        onClick={() => onDecide('rejected')}
                    >
                        {t('registry.reject')}
                    </button>
                    <button
                        type="button"
                        className="button button-primary"
                        disabled={isBusy}
                        onClick={() => onDecide('approved')}
                    >
                        {t('registry.approve')}
                    </button>
                </div>
            )}
        </div>
    );
}
