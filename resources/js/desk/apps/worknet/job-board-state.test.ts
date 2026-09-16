import { describe, expect, it } from 'vite-plus/test';
import type { JobApplication } from '@/types';
import { applicationFor } from './job-board-state';

const application: JobApplication = {
    id: 7,
    job_opening_id: 3,
    status: 'pending',
    submitted_at: '2026-09-17T09:00:00+00:00',
};

describe('applicationFor', () => {
    it('finds the application for a job opening', () => {
        expect(applicationFor([application], 3)).toBe(application);
    });

    it('returns null when the player has not applied', () => {
        expect(applicationFor([application], 4)).toBeNull();
    });
});
