import { describe, expect, it } from 'vite-plus/test';
import type { Driver, Shipment, TourPlan } from '@/types';
import { arrivalOf, arrivesLate, plannableFor, tourCell } from './tour-cell';

const van: Driver = {
    id: 1,
    name: 'Rusty Calhoun',
    vehicle: 'van',
    capacity: 3,
    busy: ['2026-09-21 morning'],
};

const truck: Driver = {
    ...van,
    id: 2,
    name: 'Ed Tanner',
    vehicle: 'truck',
    capacity: 2,
    busy: [],
};

const parcel: Shipment = {
    id: 7,
    contents: 'shut-off valve 3/4"',
    size: 'parcel',
    sender: 'Keystone Plumbing Supply Co.',
    contact: 'Rhonda Mayfield',
    recipient: 'Flowright Plumbing & Heating',
    district: 'Maple Falls',
    due_date: '2026-09-22',
    due_slot: '13:00',
    plan: null,
    delivered_at: null,
};

const pallet: Shipment = {
    ...parcel,
    id: 8,
    contents: 'burner assembly type B',
    size: 'pallet',
};

const planned: Shipment = {
    ...parcel,
    id: 9,
    plan: {
        driver_id: 1,
        driver: 'Rusty Calhoun',
        date: '2026-09-22',
        tour: 'afternoon',
        is_own: false,
        planned_by: null,
        planned_by_npc: 'Hank Mulligan',
    },
};

const plan: TourPlan = {
    days: ['2026-09-21', '2026-09-22'],
    tours: [
        { id: 'morning', starts: '07:00', ends: '10:00' },
        { id: 'afternoon', starts: '13:00', ends: '16:00' },
    ],
    drivers: [van, truck],
};

describe('tourCell', () => {
    it('marks tours the driver cannot drive as busy', () => {
        expect(tourCell(van, [], '2026-09-21', 'morning')).toEqual({
            kind: 'busy',
        });
    });

    it('lists the stops already on the tour', () => {
        expect(
            tourCell(van, [parcel, planned], '2026-09-22', 'afternoon'),
        ).toEqual({
            kind: 'open',
            capacity: 3,
            stops: [planned],
        });
    });
});

describe('arrivesLate', () => {
    it('compares the end of the tour with the due time', () => {
        expect(
            arrivesLate(parcel, arrivalOf(plan, '2026-09-22', 'morning')),
        ).toBe(false);
        expect(
            arrivesLate(parcel, arrivalOf(plan, '2026-09-22', 'afternoon')),
        ).toBe(true);
    });
});

describe('plannableFor', () => {
    it('offers only unplanned shipments the vehicle can carry', () => {
        expect(plannableFor(van, [parcel, pallet, planned])).toEqual([parcel]);
        expect(plannableFor(truck, [parcel, pallet, planned])).toEqual([
            parcel,
            pallet,
        ]);
    });
});
