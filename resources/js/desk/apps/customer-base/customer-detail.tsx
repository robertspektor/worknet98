import { useTranslation } from '@/i18n/use-translation';
import type { Customer } from '@/types';

export function CustomerDetail({ customer }: { customer: Customer | null }) {
    const { t } = useTranslation();

    if (!customer) {
        return (
            <div className="records-detail sunken">
                <p className="muted">{t('records.select')}</p>
            </div>
        );
    }

    return (
        <div className="records-detail sunken">
            <h3 className="records-detail-title">{customer.name}</h3>
            <dl className="property-list">
                <dt>{t('records.address')}</dt>
                <dd>
                    {customer.street}
                    <br />
                    {customer.city}
                </dd>
                <dt>{t('records.phone')}</dt>
                <dd>{customer.phone}</dd>
                <dt>{t('records.email')}</dt>
                <dd>{customer.email_address}</dd>
                <dt>{t('records.notes')}</dt>
                <dd>{customer.notes}</dd>
            </dl>
        </div>
    );
}
