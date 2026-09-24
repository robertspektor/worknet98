import type { CSSProperties } from 'react';
import { usePlaceable } from '../placement/use-placeable';

const SHELL_LAYERS = 28;

/** A dome stacked from ellipses, so it keeps its shape in the room's camera. */
function shellLayers() {
    return Array.from({ length: SHELL_LAYERS }, (_, index) => {
        const height = index / (SHELL_LAYERS - 1);

        return {
            height,
            spread: Math.sqrt(1 - height * height),
        };
    });
}

export function DeskMouse({ chained = false }: { chained?: boolean }) {
    const { className, ...placeable } =
        usePlaceable<HTMLDivElement>('desk-mouse');

    return (
        <div
            className={`desk-item desk-mouse ${chained ? 'is-chained' : className}`}
            {...(chained ? {} : placeable)}
            aria-hidden="true"
        >
            <span className="mouse-pad">
                <span className="mouse-shell">
                    {shellLayers().map((layer) => (
                        <span
                            key={layer.height}
                            className="shell-layer"
                            style={
                                {
                                    '--height': layer.height,
                                    '--spread': layer.spread,
                                } as CSSProperties
                            }
                        />
                    ))}
                    <span className="mouse-split" />
                </span>
            </span>
        </div>
    );
}
