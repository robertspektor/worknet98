import { Head, usePage } from '@inertiajs/react';
import { HOME_EDITION } from '@/desk/computer/editions';
import { Workstation } from '@/desk/computer/workstation';
import { EmployeeBadge } from '@/desk/room/employee-badge';
import { Room } from '@/desk/room/room';
import { StickyNote } from '@/desk/room/sticky-note';

export default function Computer() {
    const { player, status } = usePage().props;

    return (
        <>
            <Head />
            <Room scene="home">
                <Workstation
                    edition={HOME_EDITION}
                    initialState={status === 'signed-in' ? 'booting' : 'off'}
                    needsSetup={!player}
                    accessory={<StickyNote />}
                />
                {player?.employer && (
                    <EmployeeBadge company={player.employer} />
                )}
            </Room>
        </>
    );
}
