import { usePlaceable } from '../placement/use-placeable';

export function PaperTray() {
    const { className, ...placeable } =
        usePlaceable<HTMLDivElement>('paper-tray');

    return (
        <div
            className={`desk-item paper-tray ${className}`}
            {...placeable}
            aria-hidden="true"
        >
            <span className="paper-shelf is-upper">
                <span className="paper-sheet" />
            </span>
            <span className="paper-shelf is-lower">
                <span className="paper-sheet" />
            </span>
        </div>
    );
}
