import type { Replacements } from '@/i18n/translate';
import type { ShipmentPlan } from '@/types';

type Translate = (key: string, replacements?: Replacements) => string;

export function plannedByText(plan: ShipmentPlan, t: Translate): string {
    if (plan.is_own) {
        return t('tour_planner.planned_by_you');
    }

    if (plan.planned_by) {
        return t('tour_planner.planned_by', { position: plan.planned_by });
    }

    if (plan.planned_by_npc) {
        return t('tour_planner.planned_by_npc', { name: plan.planned_by_npc });
    }

    return t('tour_planner.planned_by_office');
}
