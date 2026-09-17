import { describe, expect, it } from 'vite-plus/test';
import type { Shipment } from '@/types';
import { shipmentStatus } from './shipment-status';

const shipment: Shipment = {
    id: 1,
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

const plan = {
    driver_id: 3,
    driver: 'Rusty Calhoun',
    date: '2026-09-22',
    tour: 'morning' as const,
    is_own: true,
    planned_by: null,
    planned_by_npc: null,
};

describe('shipmentStatus', () => {
    it('is open until the shipment is on a route', () => {
        expect(shipmentStatus(shipment)).toBe('open');
    });

    it('is planned once a driver takes it along', () => {
        expect(shipmentStatus({ ...shipment, plan })).toBe('planned');
    });

    it('is delivered once it arrived', () => {
        expect(
            shipmentStatus({
                ...shipment,
                plan,
                delivered_at: '2026-09-22T10:00:00',
            }),
        ).toBe('delivered');
    });
});
