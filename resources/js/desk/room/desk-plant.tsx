import type { CSSProperties } from 'react';
import { usePlaceable } from '../placement/use-placeable';

/* A snake plant, the office survivor of 1998: stiff leaves fanning out of the
   soil, the middle ones tallest. */

const LEAVES = [
    { angle: -46, height: 58, shift: -10, width: 20 },
    { angle: -28, height: 80, shift: -6, width: 21 },
    { angle: -12, height: 95, shift: -3, width: 22 },
    { angle: 2, height: 100, shift: 0, width: 23 },
    { angle: 15, height: 90, shift: 3, width: 21 },
    { angle: 30, height: 74, shift: 7, width: 20 },
    { angle: 48, height: 60, shift: 10, width: 19 },
];

export function DeskPlant({ spot = 'desk-plant' }: { spot?: string }) {
    const { className, ...placeable } = usePlaceable<HTMLDivElement>(spot);

    return (
        <div
            className={`desk-item desk-plant ${spot} ${className}`}
            {...placeable}
            aria-hidden="true"
        >
            <span className="plant-leaves">
                {LEAVES.map((leaf) => (
                    <span
                        key={leaf.angle}
                        className="plant-leaf"
                        style={
                            {
                                '--angle': `${leaf.angle}deg`,
                                '--height': `${leaf.height}%`,
                                '--shift': `${leaf.shift}%`,
                                '--width': `${leaf.width}%`,
                            } as CSSProperties
                        }
                    >
                        <span className="leaf-blade" />
                    </span>
                ))}
            </span>
            <span className="plant-pot">
                <span className="pot-soil" />
                <span className="pot-rim" />
            </span>
        </div>
    );
}
