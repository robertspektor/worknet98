import { APPS_ICONS } from './icons/apps';
import { MINEFIELD_ICONS } from './icons/minefield';
import { SYSTEM_ICONS } from './icons/system';

export const PALETTE: Record<string, string> = {
    k: '#000000',
    w: '#ffffff',
    g: '#c0c0c0',
    d: '#808080',
    n: '#000080',
    B: '#1060e0',
    t: '#008080',
    y: '#ffd800',
    o: '#8a5a19',
    O: '#c98b3a',
    r: '#d02020',
    G: '#20a040',
};

export const ICONS = {
    ...SYSTEM_ICONS,
    ...APPS_ICONS,
    ...MINEFIELD_ICONS,
} satisfies Record<string, string[]>;

export type IconName = keyof typeof ICONS;

export const GLYPHS = {
    minimize: [
        '........',
        '........',
        '........',
        '........',
        '........',
        '.kkkkkk.',
        '.kkkkkk.',
    ],
    close: [
        'kk....kk',
        '.kk..kk.',
        '..kkkk..',
        '...kk...',
        '..kkkk..',
        '.kk..kk.',
        'kk....kk',
    ],
} satisfies Record<string, string[]>;

export type GlyphName = keyof typeof GLYPHS;
