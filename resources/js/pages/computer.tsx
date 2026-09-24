import { Head, usePage, usePoll } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { HomeWorkstation } from '@/desk/computer/home-workstation';
import { FloppyDriveProvider } from '@/desk/floppy/floppy-drive-provider';
import { HomeComputerProvider } from '@/desk/hardware/home-computer-provider';
import { WorkbenchProvider } from '@/desk/hardware/workbench-provider';
import { ComputerProvider } from '@/desk/computer/computer-provider';
import { useRunningBodyClass } from '@/desk/computer/use-running-class';
import { CameraStage } from '@/desk/camera/camera-stage';
import { ParcelProvider } from '@/desk/parcels/parcel-provider';
import { PlacementProvider } from '@/desk/placement/placement-provider';
import { DeskWorkshop } from '@/desk/room/desk-workshop';
import { EmployeeBadge } from '@/desk/room/employee-badge';
import { FloppyBox } from '@/desk/room/floppy-box';
import { ParcelStack } from '@/desk/room/parcel-stack';
import { DeskMouse } from '@/desk/room/desk-mouse';
import { DeskPlant } from '@/desk/room/desk-plant';
import { Keyboard } from '@/desk/room/keyboard';
import { PcTower } from '@/desk/room/pc-tower';
import { RoomLight } from '@/desk/room/room-light';
import { RoomShell } from '@/desk/room/room-shell';
import { WallCalendar } from '@/desk/room/wall-calendar';
import { WallPoster } from '@/desk/room/wall-poster';
import { Room } from '@/desk/room/room';
import { StickyNote } from '@/desk/room/sticky-note';
import { ArrivalVeil } from '@/desk/travel/arrival-veil';

const PLAYER_POLL_INTERVAL_MS = 15_000;

/* Switched off the room is in the picture; switched on the screen moves in. */

function Scene({ children }: { children: ReactNode }) {
    useRunningBodyClass();

    return (
        <CameraStage>
            <Room scene="home">{children}</Room>
        </CameraStage>
    );
}

/* Coming home from the public terminal: a returning player switched their
   machine on before they left, a new one has never touched it. */

function initialState(status: string | null): 'booting' | 'off' {
    return status === 'returning' ? 'booting' : 'off';
}

export default function Computer() {
    const { player, status, city } = usePage<{ city: string | null }>().props;
    const isSignedIn = player !== null;
    const isArriving = status === 'arriving' || status === 'returning';
    usePoll(PLAYER_POLL_INTERVAL_MS, { only: ['player'] });

    return (
        <>
            <Head />
            <HomeComputerProvider isSignedIn={isSignedIn}>
                <FloppyDriveProvider isSignedIn={isSignedIn}>
                    <ParcelProvider isSignedIn={isSignedIn}>
                        <ComputerProvider initialState={initialState(status)}>
                            <PlacementProvider isSignedIn={isSignedIn}>
                                <WorkbenchProvider>
                                    <Scene>
                                        <HomeWorkstation
                                            accessory={<StickyNote />}
                                        />
                                        <RoomShell />
                                        <WallCalendar />
                                        {city && <WallPoster city={city} />}
                                        <PcTower />
                                        <Keyboard />
                                        <DeskMouse />
                                        <DeskPlant />
                                        {player && <FloppyBox />}
                                        {player && <ParcelStack />}
                                        {player && <DeskWorkshop />}
                                        {player?.employer && (
                                            <EmployeeBadge
                                                company={player.employer}
                                            />
                                        )}
                                        <RoomLight />
                                    </Scene>
                                </WorkbenchProvider>
                                {isArriving && (
                                    <ArrivalVeil
                                        city={city}
                                        isFirstDay={status === 'arriving'}
                                    />
                                )}
                            </PlacementProvider>
                        </ComputerProvider>
                    </ParcelProvider>
                </FloppyDriveProvider>
            </HomeComputerProvider>
        </>
    );
}
