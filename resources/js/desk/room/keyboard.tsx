import { usePlaceable } from '../placement/use-placeable';

const ROWS = [
    [1, -1, 1, 1, 1, 1, -0.5, 1, 1, 1, 1, -0.5, 1, 1, 1, 1],
    [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2],
    [1.5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1.5],
    [1.75, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2.25],
    [2.25, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2.75],
    [1.25, 1.25, 1.25, 6.25, 1.25, 1.25, 1.25, 1.25],
];

export function Keyboard({ chained = false }: { chained?: boolean }) {
    const { className, ...placeable } =
        usePlaceable<HTMLDivElement>('keyboard');

    return (
        <div
            className={`desk-item keyboard ${chained ? 'is-chained' : className}`}
            {...(chained ? {} : placeable)}
            aria-hidden="true"
        >
            <span className="keyboard-deck">
                {ROWS.map((row, rowIndex) => (
                    <span className="key-row" key={rowIndex}>
                        {row.map((units, keyIndex) => (
                            <span
                                className={units < 0 ? 'key-gap' : 'key'}
                                key={keyIndex}
                                style={{ flex: `${Math.abs(units)} 0 0` }}
                            />
                        ))}
                    </span>
                ))}
            </span>
            <span className="keyboard-lip">
                <span className="keyboard-brand">RetroTron</span>
            </span>
            {chained && <span className="keyboard-chain" />}
        </div>
    );
}
