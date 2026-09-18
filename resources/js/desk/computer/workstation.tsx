import type { ReactNode } from 'react';
import { BootSequence } from '../boot/boot-sequence';
import { ShutDownScreen } from '../boot/shut-down-screen';
import { ThermalFaultScreen } from '../boot/thermal-fault-screen';
import { useOverheating } from '../hardware/use-overheating';
import { Desktop } from '../os/desktop';
import { Monitor } from '../room/monitor';
import { SetupScreen } from '../setup/setup-screen';
import { sound } from '../sound/sound';
import type { ComputerState } from './computer-state';
import { EditionContext } from './edition-context';
import type { Edition } from './editions';
import { useReportPower } from './power-state';
import { useComputer } from './use-computer';

export function Workstation({
    edition,
    initialState = 'off',
    needsSetup = false,
    powerActionLabel,
    accessory,
}: {
    edition: Edition;
    initialState?: ComputerState;
    needsSetup?: boolean;
    powerActionLabel?: string;
    accessory?: ReactNode;
}) {
    const computer = useComputer(initialState);
    useReportPower(computer.isOn);
    useOverheating(
        edition.cpu.needsThermalPaste && computer.state === 'ready',
        computer.overheat,
    );

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
                {computer.state === 'thermal-fault' && <ThermalFaultScreen />}
            </Monitor>
            {powerActionLabel !== undefined && !computer.isOn && (
                <button
                    type="button"
                    className="power-action"
                    onClick={pressPower}
                >
                    {powerActionLabel}
                </button>
            )}
        </EditionContext>
    );
}
