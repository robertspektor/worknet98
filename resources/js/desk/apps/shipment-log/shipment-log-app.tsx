import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { index as shipmentsIndex } from '@/routes/api/v1/shipments';
import type { Shipment } from '@/types';
import { useApiResource } from '../../api/use-api-resource';
import { useCompanySoftware } from '../../company-software/company-software-provider';
import { AppLoading } from '../../ui/app-loading';
import { formatDay } from '../../ui/format';
import { ShipmentDetail } from './shipment-detail';
import { shipmentStatus } from './shipment-status';

export function ShipmentLogApp() {
    const { t, locale } = useTranslation();
    const software = useCompanySoftware();
    const { data: shipments } = useApiResource<Shipment[]>(
        shipmentsIndex.url(),
    );
    const [selectedId, setSelectedId] = useState<number | null>(null);

    if (!shipments) {
        return <AppLoading />;
    }

    return (
        <div className="company-app">
            <header className="company-app-bar">
                <span className="company-app-name">
                    {software?.app_names.shipments}
                </span>
                <span className="company-app-company">{software?.company}</span>
            </header>
            <div className="records-body shipments-body">
                <div className="records-list sunken" role="listbox">
                    {shipments.length === 0 && (
                        <p className="muted records-empty">
                            {t('shipments.empty')}
                        </p>
                    )}
                    {shipments.map((shipment) => (
                        <button
                            key={shipment.id}
                            type="button"
                            role="option"
                            aria-selected={shipment.id === selectedId}
                            className={`records-row shipment-row is-${shipmentStatus(shipment)} ${shipment.id === selectedId ? 'is-selected' : ''}`}
                            onClick={() => setSelectedId(shipment.id)}
                        >
                            <span title={shipment.contents}>
                                {shipment.contents}
                            </span>
                            <span>
                                {formatDay(shipment.due_date, locale)}{' '}
                                {shipment.due_slot}
                            </span>
                        </button>
                    ))}
                </div>
                <ShipmentDetail
                    shipment={
                        shipments.find(
                            (shipment) => shipment.id === selectedId,
                        ) ?? null
                    }
                />
            </div>
        </div>
    );
}
