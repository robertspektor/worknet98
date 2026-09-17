import { describe, expect, it } from 'vite-plus/test';
import type { CompanySoftware } from '@/types';
import { actionsFor } from './compose-actions';

const software = (appNames: CompanySoftware['app_names']): CompanySoftware => ({
    company: 'Flowright Plumbing & Heating',
    branch: 'Maple Falls',
    office_address: 'office@flowright.wn',
    app_names: appNames,
});

describe('actionsFor', () => {
    it('offers appointment confirmations to companies with a scheduler', () => {
        expect(actionsFor(software({ scheduler: 'Scheduler' }))).toEqual([
            'confirm_appointment',
            'request_details',
            'other',
        ]);
    });

    it('offers route confirmations to companies with a route planner', () => {
        expect(actionsFor(software({ tours: 'Route Planner' }))).toEqual([
            'confirm_shipment',
            'request_details',
            'other',
        ]);
    });
});
