import type { ReactNode } from 'react';
import { CrtScreen } from '../screen/crt-screen';
import { PowerButton } from './power-button';

export function Monitor({
    isOn,
    isHinting = false,
    onPowerPress,
    accessory,
    children,
}: {
    isOn: boolean;
    isHinting?: boolean;
    onPowerPress: () => void;
    accessory?: ReactNode;
    children: ReactNode;
}) {
    return (
        <div className="monitor">
            {accessory}
            <div className="monitor-case" data-surface="ledge">
                <div className="screen-frame">
                    <CrtScreen isOn={isOn}>{children}</CrtScreen>
                </div>
                <div className="monitor-chin">
                    <span className="brand">RETROTRON</span>
                    <div className="vents" aria-hidden="true">
                        <span />
                        <span />
                        <span />
                    </div>
                    <PowerButton
                        isOn={isOn}
                        isHinting={isHinting}
                        onPress={onPowerPress}
                    />
                </div>
            </div>
            <div className="stand" />
        </div>
    );
}
