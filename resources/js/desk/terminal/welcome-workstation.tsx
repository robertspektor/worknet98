import { useComputerMachine } from '../computer/computer-provider';
import { Monitor } from '../room/monitor';
import { DisconnectScreen } from './disconnect-screen';
import { WelcomeScreen } from './welcome-screen';

export function WelcomeWorkstation({
    address,
    city,
    isNewResident,
    isLeaving,
    onLeave,
}: {
    address: string;
    city: string | null;
    isNewResident: boolean;
    isLeaving: boolean;
    onLeave: () => void;
}) {
    const computer = useComputerMachine();

    return (
        <Monitor isOn={computer.isOn}>
            {isLeaving ? (
                <DisconnectScreen city={city} />
            ) : (
                <WelcomeScreen
                    address={address}
                    city={city}
                    isNewResident={isNewResident}
                    onLeave={onLeave}
                />
            )}
        </Monitor>
    );
}
