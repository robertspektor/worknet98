import { Head, router, usePage } from '@inertiajs/react';
import {
    ComputerProvider,
    useComputerMachine,
} from '@/desk/computer/computer-provider';
import { PlacementProvider } from '@/desk/placement/placement-provider';
import { DeskForm } from '@/desk/room/desk-form';
import { DeskLamp } from '@/desk/room/desk-lamp';
import { FilingCabinet } from '@/desk/room/filing-cabinet';
import { Keyboard } from '@/desk/room/keyboard';
import { PublicTerminal } from '@/desk/room/public-terminal';
import { Room } from '@/desk/room/room';
import { TerminalNotice } from '@/desk/room/terminal-notice';
import { WallClock } from '@/desk/room/wall-clock';
import { WelcomeWorkstation } from '@/desk/terminal/welcome-workstation';
import { Blackout } from '@/desk/travel/blackout';
import { useDeparture } from '@/desk/travel/use-departure';
import { leave } from '@/routes/terminal';

function Stage({
    address,
    city,
    isNewResident,
}: {
    address: string;
    city: string | null;
    isNewResident: boolean;
}) {
    const computer = useComputerMachine();
    const { phase, depart } = useDeparture({
        onDark: computer.togglePower,
        onLeave: () => router.post(leave.url()),
    });

    return (
        <>
            <div className="landing-room is-running">
                <Room scene="terminal">
                    <WelcomeWorkstation
                        address={address}
                        city={city}
                        isNewResident={isNewResident}
                        isLeaving={phase !== 'here'}
                        onLeave={depart}
                    />
                    <PublicTerminal city={city} />
                    <Keyboard chained />
                    <DeskLamp />
                    <WallClock />
                    <FilingCabinet />
                    <DeskForm />
                    <TerminalNotice city={city} />
                </Room>
            </div>
            <Blackout isDark={phase === 'dark' || phase === 'leaving'} />
        </>
    );
}

export default function Terminal() {
    const { address, city, status } = usePage<{
        address: string;
        city: string | null;
    }>().props;

    return (
        <>
            <Head />
            <ComputerProvider initialState="ready">
                <PlacementProvider isSignedIn={false}>
                    <Stage
                        address={address}
                        city={city}
                        isNewResident={status === 'registered'}
                    />
                </PlacementProvider>
            </ComputerProvider>
        </>
    );
}
