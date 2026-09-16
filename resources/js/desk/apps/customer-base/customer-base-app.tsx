import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { index as customersIndex } from '@/routes/api/v1/customers';
import type { Customer } from '@/types';
import { useApiResource } from '../../api/use-api-resource';
import { useCompanySoftware } from '../../company-software/company-software-provider';
import { AppLoading } from '../../ui/app-loading';
import { CustomerDetail } from './customer-detail';
import { searchCustomers } from './customer-search';

export function CustomerBaseApp() {
    const { t } = useTranslation();
    const software = useCompanySoftware();
    const { data: customers } = useApiResource<Customer[]>(
        customersIndex.url(),
    );
    const [query, setQuery] = useState('');
    const [selectedId, setSelectedId] = useState<number | null>(null);

    if (!customers) {
        return <AppLoading />;
    }

    const results = searchCustomers(customers, query);

    return (
        <div className="company-app">
            <header className="company-app-bar">
                <span className="company-app-name">
                    {software?.app_names.records}
                </span>
                <span className="company-app-company">{software?.company}</span>
            </header>
            <label className="records-search" htmlFor="records-search">
                <span>{t('records.search')}</span>
                <input
                    id="records-search"
                    className="input"
                    value={query}
                    onChange={(event) => setQuery(event.target.value)}
                />
            </label>
            <div className="records-body">
                <div className="records-list sunken" role="listbox">
                    {results.length === 0 && (
                        <p className="muted records-empty">
                            {t('records.no_results')}
                        </p>
                    )}
                    {results.map((customer) => (
                        <button
                            key={customer.id}
                            type="button"
                            role="option"
                            aria-selected={customer.id === selectedId}
                            className={`records-row ${customer.id === selectedId ? 'is-selected' : ''}`}
                            onClick={() => setSelectedId(customer.id)}
                        >
                            <span>{customer.name}</span>
                            <span>{customer.city}</span>
                        </button>
                    ))}
                </div>
                <CustomerDetail
                    customer={
                        customers.find(
                            (customer) => customer.id === selectedId,
                        ) ?? null
                    }
                />
            </div>
        </div>
    );
}
