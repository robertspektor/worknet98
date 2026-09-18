import { Head, usePage, usePoll } from '@inertiajs/react';
import { HomeWorkstation } from '@/desk/computer/home-workstation';
import { FloppyDriveProvider } from '@/desk/floppy/floppy-drive-provider';
import { HomeComputerProvider } from '@/desk/hardware/home-computer-provider';
import { PowerStateProvider } from '@/desk/computer/power-state';
import { ParcelProvider } from '@/desk/parcels/parcel-provider';
import { PlacementProvider } from '@/desk/placement/placement-provider';
import { DeskWorkshop } from '@/desk/room/desk-workshop';
import { EmployeeBadge } from '@/desk/room/employee-badge';
import { FloppyBox } from '@/desk/room/floppy-box';
import { ParcelStack } from '@/desk/room/parcel-stack';
import { PcTower } from '@/desk/room/pc-tower';
import { Room } from '@/desk/room/room';
import { StickyNote } from '@/desk/room/sticky-note';

const PLAYER_POLL_INTERVAL_MS = 15_000;

function initialState(status: string | null): 'ready' | 'booting' | 'off' {
    if (status === 'booted') {
        return 'ready';
    }

    return status === 'signed-in' ? 'booting' : 'off';
}

export default function Computer() {
    const { player, status } = usePage().props;
    const isSignedIn = player !== null;
    usePoll(PLAYER_POLL_INTERVAL_MS, { only: ['player'] });

    return (
        <>
            <Head />
            <HomeComputerProvider isSignedIn={isSignedIn}>
                <FloppyDriveProvider isSignedIn={isSignedIn}>
                    <ParcelProvider isSignedIn={isSignedIn}>
                        <PowerStateProvider>
                            <PlacementProvider isSignedIn={isSignedIn}>
                                <Room scene="home">
                                    <HomeWorkstation
                                        initialState={initialState(status)}
                                        accessory={<StickyNote />}
                                    />
                                    <PcTower />
                                    {player && <FloppyBox />}
                                    {player && <ParcelStack />}
                                    {player && <DeskWorkshop />}
                                    {player?.employer && (
                                        <EmployeeBadge
                                            company={player.employer}
                                        />
                                    )}
                                </Room>
                            </PlacementProvider>
                        </PowerStateProvider>
                    </ParcelProvider>
                </FloppyDriveProvider>
            </HomeComputerProvider>
        </>
    );
}
