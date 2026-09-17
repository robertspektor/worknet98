import { describe, expect, it } from 'vite-plus/test';
import type { CivilApplication } from '@/types';
import { applicationStatus, decidedByText } from './application-status';

const application: CivilApplication = {
    id: 1,
    kind: 'move',
    applicant: 'Margaret Hollis',
    claimed_street: '14 Birch Lane',
    claimed_district: 'Maple Falls',
    partner: null,
    claimed_partner_street: null,
    claimed_partner_district: null,
    detail: null,
    new_street: '99 Sunset Drive',
    new_district: 'Brookhaven',
    moved_on: '1998-01-21',
    decision: null,
    is_own: true,
    decided_by: null,
    decided_by_npc: null,
};

const t = (key: string, replacements?: Record<string, string | number>) =>
    `${key}${replacements ? JSON.stringify(replacements) : ''}`;

describe('applicationStatus', () => {
    it('is open until decided', () => {
        expect(applicationStatus(application)).toBe('open');
        expect(
            applicationStatus({ ...application, decision: 'rejected' }),
        ).toBe('rejected');
    });
});

describe('decidedByText', () => {
    it('says nothing about own open applications', () => {
        expect(decidedByText(application, t)).toBeNull();
    });

    it('names the NPC colleague who decided', () => {
        expect(
            decidedByText(
                {
                    ...application,
                    is_own: false,
                    decision: 'approved',
                    decided_by_npc: 'Marvin Cole',
                },
                t,
            ),
        ).toBe('registry.decided_by_npc{"name":"Marvin Cole"}');
    });
});
