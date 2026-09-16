import type { MouseEvent } from 'react';
import { PixelIcon } from '../../ui/pixel-icon';
import type { Cell } from './minefield-state';

function CellContent({ cell }: { cell: Cell }) {
    if (!cell.isOpen) {
        return cell.isFlagged ? <PixelIcon name="flag" size={16} /> : null;
    }

    if (cell.isMine) {
        return <PixelIcon name="mine" size={16} />;
    }

    return cell.adjacentMines > 0 ? cell.adjacentMines : null;
}

export function MinefieldCell({
    cell,
    isExploded,
    onReveal,
    onFlag,
}: {
    cell: Cell;
    isExploded: boolean;
    onReveal: () => void;
    onFlag: () => void;
}) {
    const flag = (event: MouseEvent) => {
        event.preventDefault();
        onFlag();
    };

    return (
        <button
            type="button"
            className={`minefield-cell ${cell.isOpen ? 'is-open' : ''} ${isExploded ? 'is-exploded' : ''} is-${cell.adjacentMines}`}
            onClick={onReveal}
            onContextMenu={flag}
        >
            <CellContent cell={cell} />
        </button>
    );
}
