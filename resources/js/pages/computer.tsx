import { Head, usePage, usePoll } from '@inertiajs/react';
import { HomeWorkstation } from '@/desk/computer/home-workstation';
import { FloppyDriveProvider } from '@/desk/floppy/floppy-drive-provider';
import { HomeComputerProvider } from '@/desk/hardware/home-computer-provider';
import { EmployeeBadge } from '@/desk/room/employee-badge';
import { FloppyBox } from '@/desk/room/floppy-box';
import { ParcelStack } from '@/desk/room/parcel-stack';
import { PcTower } from '@/desk/room/pc-tower';
import { Room } from '@/desk/room/room';
import { StickyNote } from '@/desk/room/sticky-note';

const PLAYER_POLL_INTERVAL_MS = 15_000;

export default function Computer() {
    const { player, status } = usePage().props;
    usePoll(PLAYER_POLL_INTERVAL_MS, { only: ['player'] });

    return (
        <>
            <Head />
            <HomeComputerProvider isSignedIn={player !== null}>
                <FloppyDriveProvider isSignedIn={player !== null}>
                    <Room scene="home">
                        <HomeWorkstation
                            initialState={
                                status === 'signed-in' ? 'booting' : 'off'
                            }
                            needsSetup={!player}
                            accessory={<StickyNote />}
                        />
                        <PcTower />
                        {player && <FloppyBox />}
                        {player && <ParcelStack />}
                        {player?.employer && (
                            <EmployeeBadge company={player.employer} />
                        )}
                    </Room>
                </FloppyDriveProvider>
            </HomeComputerProvider>
        </>
    );
}
