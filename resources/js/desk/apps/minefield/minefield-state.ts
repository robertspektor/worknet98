export type Cell = {
    isMine: boolean;
    isOpen: boolean;
    isFlagged: boolean;
    adjacentMines: number;
};

export type MinefieldStatus = 'ready' | 'playing' | 'won' | 'lost';

export type Minefield = {
    rows: number;
    columns: number;
    mineCount: number;
    cells: Cell[];
    status: MinefieldStatus;
};

export type Random = () => number;

const closedCell: Cell = {
    isMine: false,
    isOpen: false,
    isFlagged: false,
    adjacentMines: 0,
};

export function createMinefield(
    rows: number,
    columns: number,
    mineCount: number,
): Minefield {
    return {
        rows,
        columns,
        mineCount,
        cells: Array.from({ length: rows * columns }, () => closedCell),
        status: 'ready',
    };
}

export function reveal(
    field: Minefield,
    index: number,
    random: Random,
): Minefield {
    if (
        isOver(field) ||
        field.cells[index].isOpen ||
        field.cells[index].isFlagged
    ) {
        return field;
    }

    const armed =
        field.status === 'ready' ? layMines(field, index, random) : field;

    if (armed.cells[index].isMine) {
        return explode(armed);
    }

    const opened = { ...armed, cells: floodOpen(armed, index) };

    return { ...opened, status: isCleared(opened) ? 'won' : 'playing' };
}

export function toggleFlag(field: Minefield, index: number): Minefield {
    const cell = field.cells[index];

    if (isOver(field) || cell.isOpen) {
        return field;
    }

    return {
        ...field,
        cells: field.cells.with(index, { ...cell, isFlagged: !cell.isFlagged }),
    };
}

export function minesLeft(field: Minefield): number {
    return (
        field.mineCount - field.cells.filter((cell) => cell.isFlagged).length
    );
}

export function isOver(field: Minefield): boolean {
    return field.status === 'won' || field.status === 'lost';
}

export function neighbours(field: Minefield, index: number): number[] {
    const row = Math.floor(index / field.columns);
    const column = index % field.columns;
    const result: number[] = [];

    for (let r = row - 1; r <= row + 1; r++) {
        for (let c = column - 1; c <= column + 1; c++) {
            const isInside =
                r >= 0 && r < field.rows && c >= 0 && c < field.columns;

            if (isInside && !(r === row && c === column)) {
                result.push(r * field.columns + c);
            }
        }
    }

    return result;
}

function layMines(
    field: Minefield,
    safeIndex: number,
    random: Random,
): Minefield {
    const safe = new Set([safeIndex, ...neighbours(field, safeIndex)]);
    const candidates = field.cells
        .map((_, index) => index)
        .filter((index) => !safe.has(index));
    const mines = new Set(pick(candidates, field.mineCount, random));
    const cells = field.cells.map((cell, index) => ({
        ...cell,
        isMine: mines.has(index),
        adjacentMines: neighbours(field, index).filter((neighbour) =>
            mines.has(neighbour),
        ).length,
    }));

    return { ...field, cells };
}

function pick(candidates: number[], count: number, random: Random): number[] {
    const pool = [...candidates];
    const picked: number[] = [];

    while (picked.length < count && pool.length > 0) {
        picked.push(pool.splice(Math.floor(random() * pool.length), 1)[0]);
    }

    return picked;
}

function floodOpen(field: Minefield, start: number): Cell[] {
    const cells = [...field.cells];
    const queue = [start];

    while (queue.length > 0) {
        const index = queue.pop() as number;
        const cell = cells[index];

        if (cell.isOpen || cell.isFlagged || cell.isMine) {
            continue;
        }

        cells[index] = { ...cell, isOpen: true };

        if (cell.adjacentMines === 0) {
            queue.push(...neighbours(field, index));
        }
    }

    return cells;
}

function explode(field: Minefield): Minefield {
    return {
        ...field,
        status: 'lost',
        cells: field.cells.map((cell) =>
            cell.isMine ? { ...cell, isOpen: true } : cell,
        ),
    };
}

function isCleared(field: Minefield): boolean {
    return field.cells.every((cell) => cell.isMine || cell.isOpen);
}
