import type { ReactNode } from 'react';
import { BootSequence } from '../boot/boot-sequence';
import { ShutDownScreen } from '../boot/shut-down-screen';
import { ThermalFaultScreen } from '../boot/thermal-fault-screen';
import { useOverheating } from '../hardware/use-overheating';
import { Desktop } from '../os/desktop';
import { Monitor } from '../room/monitor';
import { useComputerMachine } from './computer-provider';
import { EditionContext } from './edition-context';
import type { Edition } from './editions';

export function Workstation({
    edition,
    accessory,
}: {
    edition: Edition;
    accessory?: ReactNode;
}) {
    const computer = useComputerMachine();
    useOverheating(
        edition.cpu.needsThermalPaste && computer.state === 'ready',
        computer.overheat,
    );

    return (
        <EditionContext value={edition}>
            <Monitor isOn={computer.isOn} accessory={accessory}>
                {computer.state === 'booting' && (
                    <BootSequence onDone={computer.finishBoot} />
                )}
                {computer.state === 'ready' && (
                    <Desktop onShutDown={computer.shutDown} />
                )}
                {computer.state === 'shut-down' && <ShutDownScreen />}
                {computer.state === 'thermal-fault' && <ThermalFaultScreen />}
            </Monitor>
        </EditionContext>
    );
}
