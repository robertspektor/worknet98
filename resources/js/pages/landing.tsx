import { Head, usePage } from '@inertiajs/react';
import { ComputerProvider } from '@/desk/computer/computer-provider';
import { useRunningBodyClass } from '@/desk/computer/use-running-class';
import { PlacementProvider } from '@/desk/placement/placement-provider';
import { DeskForm } from '@/desk/room/desk-form';
import { DeskLamp } from '@/desk/room/desk-lamp';
import { FilingCabinet } from '@/desk/room/filing-cabinet';
import { Keyboard } from '@/desk/room/keyboard';
import { PublicTerminal } from '@/desk/room/public-terminal';
import { Room } from '@/desk/room/room';
import { TerminalNotice } from '@/desk/room/terminal-notice';
import { WallClock } from '@/desk/room/wall-clock';
import { AccessWorkstation } from '@/desk/terminal/access-workstation';
import type { TerminalScreen } from '@/desk/terminal/terminal-state';
import { useTranslation } from '@/i18n/use-translation';
import type { CityFigures } from '@/types';

function Stage({
    city,
    figures,
    initialScreen,
}: {
    city: string | null;
    figures: CityFigures | null;
    initialScreen: TerminalScreen;
}) {
    const { t } = useTranslation();
    const isRunning = useRunningBodyClass();

    return (
        <div className={`landing-room ${isRunning ? 'is-running' : ''}`}>
            <Room scene="terminal">
                <AccessWorkstation
                    city={city}
                    figures={figures}
                    initialScreen={initialScreen}
                />
                <PublicTerminal
                    city={city}
                    powerLabel={t('terminal.power_on')}
                />
                <Keyboard chained />
                <DeskLamp />
                <WallClock />
                <FilingCabinet />
                <DeskForm />
                <TerminalNotice city={city} />
            </Room>
        </div>
    );
}

export default function Landing() {
    const { city, figures, status } = usePage<{
        city: string | null;
        figures: CityFigures | null;
    }>().props;

    /* A dead sign-in link brings the visitor back here: the terminal is
       already running and waiting on the sign-in form. */

    const isDeadLink = status === 'login-link-invalid';

    return (
        <>
            <Head />
            <ComputerProvider initialState={isDeadLink ? 'booting' : 'off'}>
                <PlacementProvider isSignedIn={false}>
                    <Stage
                        city={city}
                        figures={figures}
                        initialScreen={isDeadLink ? 'sign-in' : 'menu'}
                    />
                </PlacementProvider>
            </ComputerProvider>
        </>
    );
}
