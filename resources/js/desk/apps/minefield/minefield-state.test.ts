import { describe, expect, it } from 'vite-plus/test';
import type { Minefield, Random } from './minefield-state';
import {
    createMinefield,
    minesLeft,
    neighbours,
    reveal,
    toggleFlag,
} from './minefield-state';

function sequence(values: number[]): Random {
    let call = 0;

    return () => values[call++ % values.length];
}

function wallInTheMiddle(): Minefield {
    return reveal(createMinefield(3, 5, 3), 0, sequence([0, 0.25, 0.7]));
}

function mineIndexes(field: Minefield): number[] {
    return field.cells.flatMap((cell, index) => (cell.isMine ? [index] : []));
}

function openIndexes(field: Minefield): number[] {
    return field.cells.flatMap((cell, index) => (cell.isOpen ? [index] : []));
}

describe('minefield', () => {
    it('starts without mines', () => {
        const field = createMinefield(9, 9, 10);

        expect(field.status).toBe('ready');
        expect(mineIndexes(field)).toEqual([]);
    });

    it('lays mines on the first reveal, never on or next to the first click', () => {
        const field = wallInTheMiddle();

        expect(mineIndexes(field)).toEqual([2, 7, 12]);
        expect(
            neighbours(field, 0).some((index) => field.cells[index].isMine),
        ).toBe(false);
    });

    it('opens connected empty cells and stops at numbers', () => {
        const field = wallInTheMiddle();

        expect(field.status).toBe('playing');
        expect(openIndexes(field)).toEqual([0, 1, 5, 6, 10, 11]);
        expect(field.cells[1].adjacentMines).toBe(2);
    });

    it('wins once every safe cell is open', () => {
        const won = [3, 4, 8, 9, 13, 14].reduce(
            (field, index) => reveal(field, index, sequence([0])),
            wallInTheMiddle(),
        );

        expect(won.status).toBe('won');
    });

    it('loses on a mine and uncovers all mines', () => {
        const lost = reveal(wallInTheMiddle(), 7, sequence([0]));

        expect(lost.status).toBe('lost');
        expect(
            mineIndexes(lost).every((index) => lost.cells[index].isOpen),
        ).toBe(true);
    });

    it('ignores reveals on flagged cells and after the game is over', () => {
        const flagged = toggleFlag(wallInTheMiddle(), 2);
        const lost = reveal(wallInTheMiddle(), 2, sequence([0]));

        expect(reveal(flagged, 2, sequence([0]))).toBe(flagged);
        expect(reveal(lost, 3, sequence([0]))).toBe(lost);
        expect(toggleFlag(lost, 3)).toBe(lost);
    });

    it('counts flags against the mines left', () => {
        const field = toggleFlag(toggleFlag(createMinefield(9, 9, 10), 4), 5);

        expect(minesLeft(field)).toBe(8);
        expect(minesLeft(toggleFlag(field, 5))).toBe(9);
    });

    it('does not flag open cells', () => {
        const field = wallInTheMiddle();

        expect(toggleFlag(field, 0)).toBe(field);
    });

    it('finds the neighbours inside the grid', () => {
        const field = createMinefield(3, 3, 1);

        expect(neighbours(field, 0)).toEqual([1, 3, 4]);
        expect(neighbours(field, 4)).toHaveLength(8);
    });
});
