import { useEffect, useState } from 'react';
import { index as applicationsIndex } from '@/routes/api/v1/job-applications';
import { index as openingsIndex } from '@/routes/api/v1/job-openings';
import { store as applicationStore } from '@/routes/api/v1/job-openings/applications';
import type { JobApplication, JobOpening } from '@/types';
import { getJson, postJson } from '../../api/game-api';

type Loaded = { openings: JobOpening[]; applications: JobApplication[] };

export type JobBoard = {
    board: Loaded | null;
    hasFailed: boolean;
    reload: () => void;
    apply: (opening: JobOpening, message: string) => Promise<void>;
};

function fetchApplications(): Promise<JobApplication[]> {
    return getJson<{ data: JobApplication[] }>(applicationsIndex.url()).then(
        ({ data }) => data,
    );
}

async function fetchBoard(): Promise<Loaded> {
    const [openings, applications] = await Promise.all([
        getJson<{ data: JobOpening[] }>(openingsIndex.url()),
        fetchApplications(),
    ]);

    return { openings: openings.data, applications };
}

export function useJobBoard(refreshKey: number): JobBoard {
    const [board, setBoard] = useState<Loaded | null>(null);
    const [hasFailed, setFailed] = useState(false);
    const [attempt, setAttempt] = useState(0);

    useEffect(() => {
        let isCurrent = true;

        fetchBoard()
            .then((loaded) => isCurrent && setBoard(loaded))
            .catch(() => isCurrent && setFailed(true));

        return () => {
            isCurrent = false;
        };
    }, [attempt, refreshKey]);

    const reload = () => {
        setFailed(false);
        setAttempt(attempt + 1);
    };

    const apply = async (opening: JobOpening, message: string) => {
        const { data } = await postJson<{ data: JobApplication }>(
            applicationStore.url(opening.id),
            { message },
        );

        setBoard(
            (current) =>
                current && {
                    ...current,
                    applications: [data, ...current.applications],
                },
        );
    };

    return { board, hasFailed, reload, apply };
}
