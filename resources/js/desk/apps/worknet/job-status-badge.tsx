import { useTranslation } from '@/i18n/use-translation';
import type { JobApplication } from '@/types';

export function JobStatusBadge({
    application,
}: {
    application: JobApplication | null;
}) {
    const { t } = useTranslation();

    if (!application) {
        return null;
    }

    return (
        <span className={`job-status is-${application.status}`}>
            {t(`worknet.status.${application.status}`)}
        </span>
    );
}
