import type { ReactNode } from 'react';
import { usePlacements } from '../placement/placement-provider';

export type Scene = 'home' | 'office' | 'terminal';

export function Room({
    scene,
    children,
}: {
    scene: Scene;
    children: ReactNode;
}) {
    const { attachRoom } = usePlacements();

    return (
        <main ref={attachRoom} className={`room is-${scene}`}>
            {children}
            <div className="desk" data-surface="area" />
        </main>
    );
}
