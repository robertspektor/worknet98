import { useCompanySoftware } from '../company-software/company-software-provider';
import { useEdition } from '../computer/edition-context';
import type { AppId } from './app-registry';
import { availableApps } from './available-apps';

export function useAvailableApps(): AppId[] {
    const { apps } = useEdition();

    return availableApps(apps, useCompanySoftware());
}
