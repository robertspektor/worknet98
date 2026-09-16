import type { JobApplication } from '@/types';

export function applicationFor(
    applications: JobApplication[],
    openingId: number,
): JobApplication | null {
    return (
        applications.find(
            (application) => application.job_opening_id === openingId,
        ) ?? null
    );
}
