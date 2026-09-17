import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import type { Shipment, TourPlan } from '@/types';
import { formatDay } from '../../ui/format';
import { plannedByText } from './planned-by';
import { arrivalOf, arrivesLate, plannableFor, tourCell } from './tour-cell';
import type { SelectedTour } from './tour-grid';

export function TourPanel({
    plan,
    shipments,
    day,
    selected,
    onPlan,
    onUnplan,
}: {
    plan: TourPlan;
    shipments: Shipment[];
    day: string;
    selected: SelectedTour | null;
    onPlan: (shipmentId: number) => Promise<void>;
    onUnplan: (shipmentId: number) => Promise<void>;
}) {
    const { t, locale } = useTranslation();
    const [shipmentId, setShipmentId] = useState('');
    const [isBusy, setBusy] = useState(false);
    const driver = plan.drivers.find(
        (entry) => entry.id === selected?.driverId,
    );

    if (!selected || !driver) {
        return (
            <div className="booking-panel">
                <p className="muted">{t('tour_planner.pick_tour')}</p>
            </div>
        );
    }

    const cell = tourCell(driver, shipments, day, selected.tour);
    const stops = cell.kind === 'open' ? cell.stops : [];
    const isFull = cell.kind === 'open' && stops.length >= cell.capacity;
    const arrival = arrivalOf(plan, day, selected.tour);
    const due = (shipment: Shipment) =>
        `${formatDay(shipment.due_date, locale)} ${shipment.due_slot}`;

    const run = async (action: () => Promise<void>) => {
        setBusy(true);
        await action().finally(() => setBusy(false));
    };

    return (
        <div className="tour-panel">
            <p>
                <b>{driver.name}</b> &middot; {formatDay(day, locale)}{' '}
                {t(`tour_planner.tours.${selected.tour}`)}
            </p>
            <ul className="tour-stops">
                {stops.length === 0 && (
                    <li className="muted">{t('tour_planner.no_stops')}</li>
                )}
                {stops.map((stop) => (
                    <li key={stop.id} className="tour-stop">
                        <span
                            className={
                                arrivesLate(stop, arrival) ? 'is-late' : ''
                            }
                        >
                            {stop.contents} &rarr; {stop.recipient} (
                            {arrivesLate(stop, arrival)
                                ? t('tour_planner.too_late', { due: due(stop) })
                                : t('tour_planner.due', { due: due(stop) })}
                            )
                        </span>
                        {stop.plan?.is_own ? (
                            <button
                                type="button"
                                className="button"
                                disabled={isBusy}
                                onClick={() =>
                                    void run(() => onUnplan(stop.id))
                                }
                            >
                                {t('tour_planner.remove')}
                            </button>
                        ) : (
                            stop.plan && (
                                <span className="muted">
                                    {plannedByText(stop.plan, t)}
                                </span>
                            )
                        )}
                    </li>
                ))}
            </ul>
            {!isFull && (
                <form
                    className="booking-panel"
                    onSubmit={(event) => {
                        event.preventDefault();
                        void run(() =>
                            onPlan(Number(shipmentId)).then(() =>
                                setShipmentId(''),
                            ),
                        );
                    }}
                >
                    <label className="booking-customer" htmlFor="tour-shipment">
                        <span>{t('tour_planner.shipment')}</span>
                        <select
                            id="tour-shipment"
                            className="select"
                            value={shipmentId}
                            onChange={(event) =>
                                setShipmentId(event.target.value)
                            }
                        >
                            <option value="">
                                {t('tour_planner.choose_shipment')}
                            </option>
                            {plannableFor(driver, shipments).map((shipment) => (
                                <option key={shipment.id} value={shipment.id}>
                                    {shipment.contents} &rarr;{' '}
                                    {shipment.recipient} (
                                    {arrivesLate(shipment, arrival)
                                        ? t('tour_planner.too_late', {
                                              due: due(shipment),
                                          })
                                        : t('tour_planner.due', {
                                              due: due(shipment),
                                          })}
                                    )
                                </option>
                            ))}
                        </select>
                    </label>
                    <button
                        type="submit"
                        className="button button-primary"
                        disabled={isBusy || shipmentId === ''}
                    >
                        {t('tour_planner.plan')}
                    </button>
                </form>
            )}
        </div>
    );
}
