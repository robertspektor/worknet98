import { useEffect, useState } from 'react';
import type { Parcel } from '@/types';
import { fetchParcels, unpackParcel } from './shop-api';

const POLL_INTERVAL_MS = 30_000;

export function useParcels(isSignedIn: boolean, onUnpacked: () => void) {
    const [parcels, setParcels] = useState<Parcel[]>([]);

    useEffect(() => {
        if (!isSignedIn) {
            return;
        }

        const refresh = () =>
            void fetchParcels()
                .then(setParcels)
                .catch(() => undefined);

        refresh();
        const timer = setInterval(refresh, POLL_INTERVAL_MS);

        return () => clearInterval(timer);
    }, [isSignedIn]);

    const unpack = async (parcel: Parcel) => {
        setParcels((current) =>
            current.filter((candidate) => candidate.id !== parcel.id),
        );
        await unpackParcel(parcel);
        onUnpacked();
    };

    return { parcels, unpack };
}
