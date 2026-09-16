import { describe, expect, it } from 'vite-plus/test';
import { nextComputerState } from './computer-state';

describe('nextComputerState', () => {
    it('boots when powered on and turns off from any other state', () => {
        expect(nextComputerState('off', 'power')).toBe('booting');
        expect(nextComputerState('booting', 'power')).toBe('off');
        expect(nextComputerState('ready', 'power')).toBe('off');
        expect(nextComputerState('shut-down', 'power')).toBe('off');
    });

    it('becomes ready only after booting', () => {
        expect(nextComputerState('booting', 'boot-finished')).toBe('ready');
        expect(nextComputerState('off', 'boot-finished')).toBe('off');
    });

    it('shuts down only when ready', () => {
        expect(nextComputerState('ready', 'shut-down')).toBe('shut-down');
        expect(nextComputerState('booting', 'shut-down')).toBe('booting');
    });
});
