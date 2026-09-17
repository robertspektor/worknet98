import type { CompanySoftware } from '@/types';
import { APPS } from './app-registry';
import type { AppDefinition, AppId } from './app-registry';

export function availableApps(
    apps: AppId[],
    software: CompanySoftware | null,
): AppId[] {
    return apps.filter((id) => {
        const { softwareType }: AppDefinition = APPS[id];

        return (
            softwareType === undefined ||
            software?.app_names[softwareType] !== undefined
        );
    });
}
