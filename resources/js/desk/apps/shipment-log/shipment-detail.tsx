import { useTranslation } from '@/i18n/use-translation';
import type { Shipment } from '@/types';
import { formatDay } from '../../ui/format';
import { plannedByText } from '../tour-planner/planned-by';
import { shipmentStatus } from './shipment-status';

export function ShipmentDetail({ shipment }: { shipment: Shipment | null }) {
    const { t, locale } = useTranslation();

    if (!shipment) {
        return (
            <div className="records-detail sunken">
                <p className="muted">{t('shipments.select')}</p>
            </div>
        );
    }

    const { plan } = shipment;

    return (
        <div className="records-detail sunken">
            <h3 className="records-detail-title">{shipment.contents}</h3>
            <dl className="property-list">
                <dt>{t('shipments.size')}</dt>
                <dd>{t(`shipments.sizes.${shipment.size}`)}</dd>
                <dt>{t('shipments.sender')}</dt>
                <dd>
                    {shipment.sender}
                    {shipment.contact && (
                        <>
                            <br />
                            {shipment.contact}
                        </>
                    )}
                </dd>
                <dt>{t('shipments.recipient')}</dt>
                <dd>
                    {shipment.recipient}
                    <br />
                    {shipment.district}
                </dd>
                <dt>{t('shipments.due')}</dt>
                <dd>
                    {formatDay(shipment.due_date, locale)} {shipment.due_slot}
                </dd>
                <dt>{t('shipments.status')}</dt>
                <dd>{t(`shipments.statuses.${shipmentStatus(shipment)}`)}</dd>
                {plan && (
                    <>
                        <dt>{t('shipments.route')}</dt>
                        <dd>
                            {plan.driver}, {formatDay(plan.date, locale)}{' '}
                            {t(`tour_planner.tours.${plan.tour}`)}
                            <br />
                            <span className="muted">
                                {plannedByText(plan, t)}
                            </span>
                        </dd>
                    </>
                )}
            </dl>
        </div>
    );
}
