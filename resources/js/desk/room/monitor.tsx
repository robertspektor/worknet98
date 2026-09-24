import type { ReactNode } from 'react';
import { CrtScreen } from '../screen/crt-screen';

export function Monitor({
    isOn,
    accessory,
    children,
}: {
    isOn: boolean;
    accessory?: ReactNode;
    children: ReactNode;
}) {
    return (
        <div className="monitor" data-focus="monitor">
            {accessory}
            <div className="monitor-case" data-surface="ledge">
                <div className="screen-frame">
                    <CrtScreen isOn={isOn}>{children}</CrtScreen>
                </div>
                <div className="monitor-chin">
                    <span className="brand">RETROTRON</span>
                    <div className="monitor-vents" aria-hidden="true">
                        <span />
                        <span />
                        <span />
                    </div>
                    <span className={`monitor-led ${isOn ? 'is-on' : ''}`} />
                </div>
            </div>
            <div className="stand" aria-hidden="true">
                <span className="stand-neck" />
                <span className="stand-foot" />
            </div>
        </div>
    );
}
