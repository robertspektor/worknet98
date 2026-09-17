import type { ReactNode } from 'react';
import { createContext, use, useEffect, useState } from 'react';
import type { Parcel } from '@/types';
import { useFloppyDrive } from '../floppy/floppy-drive-provider';
import { useHomeComputer } from '../hardware/home-computer-provider';
import { fetchParcels, unpackParcel } from '../shop/shop-api';

const POLL_INTERVAL_MS = 30_000;

type Parcels = {
    parcels: Parcel[];
    unpack: (parcel: Parcel) => Promise<void>;
};

const ParcelContext = createContext<Parcels | null>(null);

function useWaitingParcels(isSignedIn: boolean) {
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

    return [parcels, setParcels] as const;
}

export function ParcelProvider({
    isSignedIn,
    children,
}: {
    isSignedIn: boolean;
    children: ReactNode;
}) {
    const [parcels, setParcels] = useWaitingParcels(isSignedIn);
    const { refreshDisks } = useFloppyDrive();
    const homeComputer = useHomeComputer();

    const unpack = async (parcel: Parcel) => {
        setParcels((current) =>
            current.filter((candidate) => candidate.id !== parcel.id),
        );
        await unpackParcel(parcel);

        if (parcel.storefront === 'diskdepot') {
            refreshDisks();
        } else {
            homeComputer.refresh();
        }
    };

    return (
        <ParcelContext value={{ parcels, unpack }}>{children}</ParcelContext>
    );
}

export function useOptionalParcels(): Parcels | null {
    return use(ParcelContext);
}

export function useParcels(): Parcels {
    const parcels = useOptionalParcels();

    if (!parcels) {
        throw new Error('useParcels must be used inside ParcelProvider.');
    }

    return parcels;
}
