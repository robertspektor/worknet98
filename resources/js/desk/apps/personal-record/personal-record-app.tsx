import type { CSSProperties } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { index as milestonesIndex } from '@/routes/api/v1/milestones';
import type { Milestone } from '@/types';
import { useApiResource } from '../../api/use-api-resource';
import { goalProgress } from '../../shift/work-time';
import { AppLoading } from '../../ui/app-loading';
import { MilestoneRow } from './milestone-row';

export function PersonalRecordApp() {
    const { t } = useTranslation();
    const { data } = useApiResource<Milestone[]>(milestonesIndex.url());

    if (!data) {
        return <AppLoading />;
    }

    const achieved = data.filter((milestone) => milestone.achieved).length;
    const progress = goalProgress(achieved, data.length);

    return (
        <div className="personal-record">
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
            <p className="personal-record-progress">
                {t('personal_record.progress', {
                    done: achieved,
                    total: data.length,
                })}
            </p>
            <ul className="personal-record-list sunken">
                {data.map((milestone) => (
                    <MilestoneRow key={milestone.key} milestone={milestone} />
                ))}
            </ul>
        </div>
    );
}
