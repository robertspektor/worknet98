import { useTranslation } from '@/i18n/use-translation';
import type { Shipment, TourId, TourPlan } from '@/types';
import { tourCell } from './tour-cell';

export type SelectedTour = { driverId: number; tour: TourId };

export function TourGrid({
    plan,
    shipments,
    day,
    selected,
    onSelect,
}: {
    plan: TourPlan;
    shipments: Shipment[];
    day: string;
    selected: SelectedTour | null;
    onSelect: (tour: SelectedTour) => void;
}) {
    const { t } = useTranslation();

    return (
        <table className="schedule-grid">
            <thead>
                <tr>
                    <th>{t('tour_planner.driver')}</th>
                    {plan.tours.map((tour) => (
                        <th key={tour.id}>
                            {t(`tour_planner.tours.${tour.id}`)} {tour.starts}-
                            {tour.ends}
                        </th>
                    ))}
                </tr>
            </thead>
            <tbody>
                {plan.drivers.map((driver) => (
                    <tr key={driver.id}>
                        <th scope="row">
                            <span className="schedule-technician">
                                {driver.name}
                            </span>
                            <span className="schedule-skills">
                                {t(`tour_planner.vehicles.${driver.vehicle}`)}
                            </span>
                        </th>
                        {plan.tours.map((tour) => {
                            const cell = tourCell(
                                driver,
                                shipments,
                                day,
                                tour.id,
                            );
                            const isSelected =
                                selected?.driverId === driver.id &&
                                selected.tour === tour.id;
                            const load =
                                cell.kind === 'open' ? cell.stops.length : 0;

                            return (
                                <td key={tour.id}>
                                    <button
                                        type="button"
                                        className={`schedule-cell tour-cell is-${cell.kind === 'busy' ? 'busy' : load > 0 ? 'booked' : 'free'} ${isSelected ? 'is-selected' : ''}`}
                                        disabled={cell.kind === 'busy'}
                                        onClick={() =>
                                            onSelect({
                                                driverId: driver.id,
                                                tour: tour.id,
                                            })
                                        }
                                    >
                                        {cell.kind === 'busy'
                                            ? t('tour_planner.busy')
                                            : t('tour_planner.load', {
                                                  load: String(load),
                                                  capacity: String(
                                                      cell.capacity,
                                                  ),
                                              })}
                                    </button>
                                </td>
                            );
                        })}
                    </tr>
                ))}
            </tbody>
        </table>
    );
}
