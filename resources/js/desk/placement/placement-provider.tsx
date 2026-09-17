import type { ReactNode } from 'react';
import { createContext, use, useEffect, useState } from 'react';
import {
    index as placementsIndex,
    store as placementsStore,
} from '@/routes/api/v1/desk-placements';
import type { DeskPlacement } from '@/types';
import { getJson, postJson } from '../api/game-api';
import type { Placement } from './snap';

type RoomSize = { width: number; height: number };

type Placements = {
    room: HTMLElement | null;
    attachRoom: (element: HTMLElement | null) => void;
    roomSize: RoomSize;
    revision: object;
    placementOf: (item: string) => Placement | null;
    place: (item: string, placement: Placement) => void;
};

const PlacementContext = createContext<Placements | null>(null);

function usePlacementsOf(isSignedIn: boolean) {
    const [placements, setPlacements] = useState<Record<string, Placement>>({});

    useEffect(() => {
        if (!isSignedIn) {
            return;
        }

        void getJson<{ data: DeskPlacement[] }>(placementsIndex.url())
            .then(({ data }) =>
                setPlacements(
                    Object.fromEntries(
                        data.map(({ item, x, y }) => [item, { x, y }]),
                    ),
                ),
            )
            .catch(() => undefined);
    }, [isSignedIn]);

    const place = (item: string, placement: Placement) => {
        setPlacements((current) => ({ ...current, [item]: placement }));
        void postJson(placementsStore.url(), { item, ...placement }).catch(
            () => undefined,
        );
    };

    return { placements, place };
}

function useRoomSize(room: HTMLElement | null): RoomSize {
    const [size, setSize] = useState<RoomSize>({ width: 0, height: 0 });

    useEffect(() => {
        if (!room) {
            return;
        }

        const observer = new ResizeObserver(() => {
            const { width, height } = room.getBoundingClientRect();
            setSize({ width, height });
        });
        observer.observe(room);

        return () => observer.disconnect();
    }, [room]);

    return size;
}

export function PlacementProvider({
    isSignedIn,
    children,
}: {
    isSignedIn: boolean;
    children: ReactNode;
}) {
    const [room, setRoom] = useState<HTMLElement | null>(null);
    const roomSize = useRoomSize(room);
    const { placements, place } = usePlacementsOf(isSignedIn);

    return (
        <PlacementContext
            value={{
                room,
                attachRoom: setRoom,
                roomSize,
                revision: placements,
                placementOf: (item) => placements[item] ?? null,
                place,
            }}
        >
            {children}
        </PlacementContext>
    );
}

export function usePlacements(): Placements {
    const placements = use(PlacementContext);

    if (!placements) {
        throw new Error('usePlacements must be used inside PlacementProvider.');
    }

    return placements;
}
