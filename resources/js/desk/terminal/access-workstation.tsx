import type { CityFigures } from '@/types';
import { useComputerMachine } from '../computer/computer-provider';
import { Monitor } from '../room/monitor';
import { AccessScreen } from './access-screen';
import { TerminalBoot } from './terminal-boot';
import type { TerminalScreen } from './terminal-state';

/* A public terminal runs no DeskOS: it boots straight into the city network
   and has nothing to shut down. */

export function AccessWorkstation({
    city,
    figures,
    initialScreen,
}: {
    city: string | null;
    figures: CityFigures | null;
    initialScreen?: TerminalScreen;
}) {
    const computer = useComputerMachine();

    return (
        <Monitor isOn={computer.isOn}>
            {computer.state === 'booting' && (
                <TerminalBoot city={city} onDone={computer.finishBoot} />
            )}
            {computer.state === 'ready' && (
                <AccessScreen
                    city={city}
                    figures={figures}
                    initialScreen={initialScreen}
                />
            )}
        </Monitor>
    );
}
