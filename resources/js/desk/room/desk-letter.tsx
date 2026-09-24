import { usePlaceable } from '../placement/use-placeable';

export function DeskLetter() {
    const { className, ...placeable } = usePlaceable<HTMLDivElement>(
        'desk-letter',
        'anywhere',
    );

    return (
        <div
            className={`desk-item desk-letter ${className}`}
            {...placeable}
            aria-hidden="true"
        >
            <span className="letter-flap" />
            <span className="letter-stamp" />
        </div>
    );
}
