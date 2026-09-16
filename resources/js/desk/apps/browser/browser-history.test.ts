import { describe, expect, it } from 'vite-plus/test';
import {
    canGoBack,
    currentPage,
    goBack,
    startAt,
    visit,
} from './browser-history';

describe('browser history', () => {
    it('starts on the given page without a way back', () => {
        const history = startAt('start');

        expect(currentPage(history)).toBe('start');
        expect(canGoBack(history)).toBe(false);
        expect(goBack(history)).toBe(history);
    });

    it('visits pages and goes back', () => {
        const history = visit(startAt('start'), 'diskdepot');

        expect(currentPage(history)).toBe('diskdepot');
        expect(currentPage(goBack(history))).toBe('start');
    });

    it('does not record the current page twice', () => {
        const history = visit(startAt('start'), 'start');

        expect(canGoBack(history)).toBe(false);
    });
});
