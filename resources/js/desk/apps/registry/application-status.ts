import type { Replacements } from '@/i18n/translate';
import type { CivilApplication } from '@/types';

type Translate = (key: string, replacements?: Replacements) => string;

export type ApplicationStatus = 'open' | 'approved' | 'rejected';

export function applicationStatus(
    application: CivilApplication,
): ApplicationStatus {
    return application.decision ?? 'open';
}

export function decidedByText(
    application: CivilApplication,
    t: Translate,
): string | null {
    if (!application.decision) {
        return application.is_own ? null : t('registry.assigned_to_colleague');
    }

    if (application.is_own) {
        return t('registry.decided_by_you');
    }

    if (application.decided_by) {
        return t('registry.decided_by', { position: application.decided_by });
    }

    return application.decided_by_npc
        ? t('registry.decided_by_npc', { name: application.decided_by_npc })
        : null;
}
