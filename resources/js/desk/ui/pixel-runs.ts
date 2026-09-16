export type PixelRun = { color: string; x: number; y: number; width: number };

export function pixelRuns(rows: string[]): PixelRun[] {
    return rows.flatMap((row, y) =>
        row.split('').reduce<PixelRun[]>((runs, color, x) => {
            if (color === '.') {
                return runs;
            }

            const last = runs.at(-1);

            if (last && last.color === color && last.x + last.width === x) {
                last.width += 1;
            } else {
                runs.push({ color, x, y, width: 1 });
            }

            return runs;
        }, []),
    );
}
