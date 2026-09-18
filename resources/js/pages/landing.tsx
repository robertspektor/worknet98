import { Head } from '@inertiajs/react';
import { HomeWorkstation } from '@/desk/computer/home-workstation';
import {
    PowerStateProvider,
    useIsPoweredOn,
} from '@/desk/computer/power-state';
import { FloppyDriveProvider } from '@/desk/floppy/floppy-drive-provider';
import { HomeComputerProvider } from '@/desk/hardware/home-computer-provider';
import { PlacementProvider } from '@/desk/placement/placement-provider';
import { PcTower } from '@/desk/room/pc-tower';
import { Room } from '@/desk/room/room';
import { StickyNote } from '@/desk/room/sticky-note';
import { useTranslation } from '@/i18n/use-translation';

function Stage() {
    const { t } = useTranslation();
    const isRunning = useIsPoweredOn();

    return (
        <div className={`landing-room ${isRunning ? 'is-running' : ''}`}>
            <Room scene="home">
                <HomeWorkstation
                    needsSetup
                    powerActionLabel={t('landing.power_on')}
                    accessory={<StickyNote />}
                />
                <PcTower />
            </Room>
        </div>
    );
}

export default function Landing() {
    return (
        <>
            <Head />
            <HomeComputerProvider isSignedIn={false}>
                <FloppyDriveProvider isSignedIn={false}>
                    <PowerStateProvider>
                        <PlacementProvider isSignedIn={false}>
                            <Stage />
                        </PlacementProvider>
                    </PowerStateProvider>
                </FloppyDriveProvider>
            </HomeComputerProvider>
        </>
    );
}
