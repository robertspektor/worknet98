import type { ReactNode } from 'react';
import type { Parcel } from '@/types';
import { useFloppyDrive } from '../floppy/floppy-drive-provider';
import { useHomeComputer } from '../hardware/home-computer-provider';
import { fetchParcels, unpackParcel } from '../shop/shop-api';
import { createDeskContext } from '../state/create-desk-context';
import { usePolledResource } from '../state/use-polled-resource';

const POLL_INTERVAL_MS = 30_000;

type Parcels = {
    parcels: Parcel[];
    unpack: (parcel: Parcel) => Promise<void>;
};

const { Context, useOptional, useRequired } =
    createDeskContext<Parcels>('Parcels');

export const useOptionalParcels = useOptional;
export const useParcels = useRequired;

export function ParcelProvider({
    isSignedIn,
    children,
}: {
    isSignedIn: boolean;
    children: ReactNode;
}) {
    const { value: parcels, setValue: setParcels } = usePolledResource(
        fetchParcels,
        POLL_INTERVAL_MS,
        { isEnabled: isSignedIn },
    );
    const { refreshDisks } = useFloppyDrive();
    const homeComputer = useHomeComputer();

    const unpack = async (parcel: Parcel) => {
        setParcels(
            (current) =>
                current?.filter((candidate) => candidate.id !== parcel.id) ??
                null,
        );
        await unpackParcel(parcel);

        if (parcel.storefront === 'diskdepot') {
            refreshDisks();
        } else {
            homeComputer.refresh();
        }
    };

    return (
        <Context value={{ parcels: parcels ?? [], unpack }}>{children}</Context>
    );
}
