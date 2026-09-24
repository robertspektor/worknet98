import { usePlaceable } from '../placement/use-placeable';

export function DeskLamp() {
    const { className, ...placeable } =
        usePlaceable<HTMLDivElement>('desk-lamp');

    return (
        <div
            className={`desk-item desk-lamp ${className}`}
            {...placeable}
            aria-hidden="true"
        >
            <span className="lamp-glow" />
            <span className="lamp-stem" />
            <span className="lamp-base" />
            <span className="lamp-head">
                <span className="lamp-bulb" />
            </span>
        </div>
    );
}
