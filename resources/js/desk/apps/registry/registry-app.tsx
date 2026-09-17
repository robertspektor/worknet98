import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { index as applicationsIndex } from '@/routes/api/v1/civil-applications';
import type { CivilApplication } from '@/types';
import { useApiResource } from '../../api/use-api-resource';
import { useCompanySoftware } from '../../company-software/company-software-provider';
import { AppLoading } from '../../ui/app-loading';
import { ApplicationsTab } from './applications-tab';
import { SearchTab } from './search-tab';

type Tab = 'applications' | 'search';

const TABS: Tab[] = ['applications', 'search'];

export function RegistryApp() {
    const { t } = useTranslation();
    const software = useCompanySoftware();
    const applications = useApiResource<CivilApplication[]>(
        applicationsIndex.url(),
    );
    const [tab, setTab] = useState<Tab>('applications');

    if (!applications.data) {
        return <AppLoading />;
    }

    const title = software?.app_names.registry ?? '';

    return (
        <div className="company-app">
            <header className="company-app-bar">
                <span className="company-app-name">{title}</span>
                <span className="company-app-company">{software?.company}</span>
            </header>
            <div className="day-tabs" role="tablist">
                {TABS.map((entry) => (
                    <button
                        key={entry}
                        type="button"
                        role="tab"
                        aria-selected={entry === tab}
                        className={`day-tab ${entry === tab ? 'is-active' : ''}`}
                        onClick={() => setTab(entry)}
                    >
                        {t(`registry.tabs.${entry}`)}
                    </button>
                ))}
            </div>
            <div className="registry-tab" hidden={tab !== 'applications'}>
                <ApplicationsTab
                    applications={applications.data}
                    title={title}
                    onChanged={applications.reload}
                />
            </div>
            <div className="registry-tab" hidden={tab !== 'search'}>
                <SearchTab />
            </div>
        </div>
    );
}
