import { Head, usePage } from '@inertiajs/react';
import { HomeWorkstation } from '@/desk/computer/home-workstation';
import { FloppyDriveProvider } from '@/desk/floppy/floppy-drive-provider';
import { EmployeeBadge } from '@/desk/room/employee-badge';
import { FloppyBox } from '@/desk/room/floppy-box';
import { ParcelStack } from '@/desk/room/parcel-stack';
import { PcTower } from '@/desk/room/pc-tower';
import { Room } from '@/desk/room/room';
import { StickyNote } from '@/desk/room/sticky-note';

export default function Computer() {
    const { player, status } = usePage().props;

    return (
        <>
            <Head />
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
        </>
    );
}
