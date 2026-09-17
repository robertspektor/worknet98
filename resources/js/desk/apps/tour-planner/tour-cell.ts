import type { Driver, Shipment, TourId, TourPlan } from '@/types';

export type TourCell =
    | { kind: 'busy' }
    | { kind: 'open'; stops: Shipment[]; capacity: number };

export function tourCell(
    driver: Driver,
    shipments: Shipment[],
    day: string,
    tour: TourId,
): TourCell {
    if (driver.busy.includes(`${day} ${tour}`)) {
        return { kind: 'busy' };
    }

    return {
        kind: 'open',
        capacity: driver.capacity,
        stops: shipments.filter(
            ({ plan }) =>
                plan?.driver_id === driver.id &&
                plan.date === day &&
                plan.tour === tour,
        ),
    };
}

export function arrivalOf(plan: TourPlan, day: string, tour: TourId): string {
    const ends = plan.tours.find((entry) => entry.id === tour)?.ends ?? '';

    return `${day} ${ends}`;
}

export function arrivesLate(shipment: Shipment, arrival: string): boolean {
    return arrival > `${shipment.due_date} ${shipment.due_slot}`;
}

export function plannableFor(
    driver: Driver,
    shipments: Shipment[],
): Shipment[] {
    return shipments.filter(
        (shipment) =>
            !shipment.plan &&
            !shipment.delivered_at &&
            (driver.vehicle === 'truck' || shipment.size === 'parcel'),
    );
}
