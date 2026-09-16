import { describe, expect, it } from 'vite-plus/test';
import { installedApps } from './installed-apps';

describe('installedApps', () => {
    it('keeps only programs this computer knows how to run', () => {
        expect(installedApps(['minefield', 'lost-program'])).toEqual([
            'minefield',
        ]);
    });
});
