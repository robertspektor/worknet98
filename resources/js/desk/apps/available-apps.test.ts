import { describe, expect, it } from 'vite-plus/test';
import type { CompanySoftware } from '@/types';
import { availableApps } from './available-apps';

const software = (appNames: CompanySoftware['app_names']): CompanySoftware => ({
    company: 'Nordwerk Logistik AG',
    branch: 'Hafenstedt',
    office_address: 'disposition@nordwerk.wn',
    app_names: appNames,
});

describe('availableApps', () => {
    it('keeps only the company software the employer runs', () => {
        expect(
            availableApps(
                ['mail', 'records', 'scheduler', 'shipments', 'tours'],
                software({ shipments: 'Sendungsliste', tours: 'Tourenplaner' }),
            ),
        ).toEqual(['mail', 'shipments', 'tours']);
    });

    it('hides all company software until it is known', () => {
        expect(availableApps(['mail', 'records'], null)).toEqual(['mail']);
    });
});
