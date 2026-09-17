import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { store as decisionStore } from '@/routes/api/v1/civil-applications/decision';
import type { ApplicationDecision, CivilApplication } from '@/types';
import { postJson } from '../../api/game-api';
import { sound } from '../../sound/sound';
import { useRefusalAlert } from '../../ui/use-refusal-alert';
import { ApplicationDetail } from './application-detail';
import { applicationStatus } from './application-status';

export function ApplicationsTab({
    applications,
    title,
    onChanged,
}: {
    applications: CivilApplication[];
    title: string;
    onChanged: () => void;
}) {
    const { t } = useTranslation();
    const alertRefusal = useRefusalAlert();
    const [selectedId, setSelectedId] = useState<number | null>(null);
    const [isBusy, setBusy] = useState(false);
    const selected =
        applications.find((application) => application.id === selectedId) ??
        null;

    const decide = async (decision: ApplicationDecision) => {
        if (!selected) {
            return;
        }

        setBusy(true);

        try {
            await postJson(decisionStore.url(selected.id), { decision });
            sound.click();
            onChanged();
        } catch (error) {
            alertRefusal(title, error);
        } finally {
            setBusy(false);
        }
    };

    return (
        <div className="records-body registry-body">
            <div className="records-list sunken" role="listbox">
                {applications.length === 0 && (
                    <p className="muted records-empty">
                        {t('registry.no_applications')}
                    </p>
                )}
                {applications.map((application) => (
                    <button
                        key={application.id}
                        type="button"
                        role="option"
                        aria-selected={application.id === selectedId}
                        className={`records-row registry-row is-${applicationStatus(application)} ${application.is_own ? 'is-own' : ''} ${application.id === selectedId ? 'is-selected' : ''}`}
                        onClick={() => setSelectedId(application.id)}
                    >
                        <span>{application.applicant}</span>
                        <span>{t(`registry.kinds.${application.kind}`)}</span>
                    </button>
                ))}
            </div>
            <ApplicationDetail
                application={selected}
                isBusy={isBusy}
                onDecide={(decision) => void decide(decision)}
            />
        </div>
    );
}
