import type { CSSProperties, ReactNode } from 'react';
import { useViewportScale } from './use-viewport-scale';

export function CrtScreen({
    isOn,
    children,
}: {
    isOn: boolean;
    children: ReactNode;
}) {
    const { screenRef, scale } = useViewportScale();

    return (
        <div ref={screenRef} className={`screen ${isOn ? 'is-on' : ''}`}>
            <div className="screen-content">
                <div
                    className="viewport"
                    style={{ '--scale': scale } as CSSProperties}
                >
                    {isOn && children}
                </div>
            </div>
        </div>
    );
}
