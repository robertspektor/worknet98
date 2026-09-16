import type { ReactNode } from 'react';
import { BootSequence } from '../boot/boot-sequence';
import { ShutDownScreen } from '../boot/shut-down-screen';
import { Desktop } from '../os/desktop';
import { Monitor } from '../room/monitor';
import { SetupScreen } from '../setup/setup-screen';
import { sound } from '../sound/sound';
import type { ComputerState } from './computer-state';
import { EditionContext } from './edition-context';
import type { Edition } from './editions';
import { useComputer } from './use-computer';

export function Workstation({
    edition,
    initialState = 'off',
    needsSetup = false,
    accessory,
}: {
    edition: Edition;
    initialState?: ComputerState;
    needsSetup?: boolean;
    accessory?: ReactNode;
}) {
    const computer = useComputer(initialState);

    const pressPower = () => {
        sound.unlock();
        computer.togglePower();
    };

    return (
        <EditionContext value={edition}>
            <Monitor
                isOn={computer.isOn}
                isHinting={computer.state === 'off'}
                onPowerPress={pressPower}
                accessory={accessory}
            >
                {computer.state === 'booting' && (
                    <BootSequence onDone={computer.finishBoot} />
                )}
                {computer.state === 'ready' &&
                    (needsSetup ? (
                        <SetupScreen />
                    ) : (
                        <Desktop onShutDown={computer.shutDown} />
                    ))}
                {computer.state === 'shut-down' && <ShutDownScreen />}
            </Monitor>
        </EditionContext>
    );
}
