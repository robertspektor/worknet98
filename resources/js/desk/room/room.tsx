import type { ReactNode } from 'react';

export type Scene = 'home' | 'office';

export function Room({
    scene,
    children,
}: {
    scene: Scene;
    children: ReactNode;
}) {
    return (
        <main className={`room is-${scene}`}>
            {children}
            <div className="desk" />
        </main>
    );
}
