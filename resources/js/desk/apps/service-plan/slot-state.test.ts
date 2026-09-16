import { describe, expect, it } from 'vite-plus/test';
import type { Schedule } from '@/types';
import { slotState } from './slot-state';

const schedule: Schedule = {
    days: ['2026-09-21', '2026-09-22'],
    slots: ['08:00', '10:00'],
    technicians: [
        {
            id: 1,
            name: 'Rita Vance',
            skills: ['plumbing'],
            busy: ['2026-09-21 08:00'],
        },
    ],
    appointments: [
        {
            id: 9,
            date: '2026-09-22',
            slot: '10:00',
            technician_id: 1,
            customer: { id: 4, name: 'Margaret Hollis' },
        },
    ],
};

describe('slotState', () => {
    it('marks slots in which the technician is busy', () => {
        expect(slotState(schedule, 1, '2026-09-21', '08:00')).toEqual({
            kind: 'busy',
        });
    });

    it('returns the appointment of a booked slot', () => {
        const state = slotState(schedule, 1, '2026-09-22', '10:00');

        expect(state.kind === 'booked' && state.appointment.id).toBe(9);
    });

    it('marks all other slots as free', () => {
        expect(slotState(schedule, 1, '2026-09-21', '10:00')).toEqual({
            kind: 'free',
        });
    });
});
