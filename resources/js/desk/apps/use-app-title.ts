import { useTranslation } from '@/i18n/use-translation';
import { useCompanySoftware } from '../company-software/company-software-provider';
import { APPS } from './app-registry';
import type { AppDefinition, AppId } from './app-registry';

export function useAppTitle(): (id: AppId) => string {
    const { t } = useTranslation();
    const software = useCompanySoftware();

    return (id) => {
        const app: AppDefinition = APPS[id];
        const companyName = app.softwareType
            ? software?.app_names[app.softwareType]
            : undefined;

        return companyName ?? t(app.titleKey);
    };
}
