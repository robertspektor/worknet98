import { describe, expect, it } from 'vite-plus/test';
import { IDLE_AFTER_MS, isActive } from './activity';

describe('activity', () => {
    it('counts the player as active shortly after an input', () => {
        expect(isActive(1_000, 1_000 + IDLE_AFTER_MS - 1)).toBe(true);
    });

    it('counts the player as idle five minutes after the last input', () => {
        expect(isActive(1_000, 1_000 + IDLE_AFTER_MS)).toBe(false);
    });
});
