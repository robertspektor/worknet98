import { describe, expect, it } from 'vite-plus/test';
import { countedMemoryKb, INSTALLED_MEMORY_KB } from './memory-count';

describe('countedMemoryKb', () => {
    it('counts up in whole megabytes', () => {
        expect(countedMemoryKb(0, 1600)).toBe(0);
        expect(countedMemoryKb(500, 1600)).toBe(5_120);
        expect(countedMemoryKb(1599, 1600)).toBe(15_360);
    });

    it('reports all memory once the test is over', () => {
        expect(countedMemoryKb(1600, 1600)).toBe(INSTALLED_MEMORY_KB);
        expect(countedMemoryKb(10, 0)).toBe(INSTALLED_MEMORY_KB);
    });
});
