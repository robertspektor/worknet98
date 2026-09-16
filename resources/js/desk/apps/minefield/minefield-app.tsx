import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { sound } from '../../sound/sound';
import type { IconName } from '../../ui/pixel-art';
import { PixelIcon } from '../../ui/pixel-icon';
import { MinefieldCell } from './minefield-cell';
import type { Minefield, MinefieldStatus } from './minefield-state';
import {
    createMinefield,
    minesLeft,
    reveal,
    toggleFlag,
} from './minefield-state';

const ROWS = 9;
const COLUMNS = 9;
const MINES = 10;

const FACES: Record<MinefieldStatus, IconName> = {
    ready: 'face-smile',
    playing: 'face-smile',
    won: 'face-cool',
    lost: 'face-dead',
};

const newField = () => createMinefield(ROWS, COLUMNS, MINES);

function Counter({ value }: { value: number }) {
    return (
        <span className="minefield-counter">
            {String(Math.max(value, 0)).padStart(3, '0')}
        </span>
    );
}

export function MinefieldApp() {
    const { t } = useTranslation();
    const [field, setField] = useState<Minefield>(newField);
    const [explodedIndex, setExplodedIndex] = useState<number | null>(null);

    const revealCell = (index: number) => {
        const next = reveal(field, index, Math.random);

        if (next.status === 'lost' && field.status !== 'lost') {
            sound.error();
            setExplodedIndex(index);
        }
        setField(next);
    };

    const restart = () => {
        setField(newField());
        setExplodedIndex(null);
    };

    return (
        <div className="minefield">
            <div className="minefield-header sunken">
                <Counter value={minesLeft(field)} />
                <button
                    type="button"
                    className="button minefield-face"
                    aria-label={t('minefield.new_game')}
                    title={t('minefield.new_game')}
                    onClick={restart}
                >
                    <PixelIcon name={FACES[field.status]} size={16} />
                </button>
                <Counter
                    value={field.cells.filter((cell) => cell.isOpen).length}
                />
            </div>
            <div
                className="minefield-grid sunken"
                style={{ gridTemplateColumns: `repeat(${COLUMNS}, 1fr)` }}
            >
                {field.cells.map((cell, index) => (
                    <MinefieldCell
                        key={index}
                        cell={cell}
                        isExploded={explodedIndex === index}
                        onReveal={() => revealCell(index)}
                        onFlag={() => setField(toggleFlag(field, index))}
                    />
                ))}
            </div>
            <p className="minefield-status">
                {t(`minefield.status_${field.status}`)}
            </p>
        </div>
    );
}
