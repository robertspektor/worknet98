import { describe, expect, it } from 'vite-plus/test';
import { nextDeparturePhase } from './departure';

describe('leaving the public terminal', () => {
    it('drops the line, then the picture, then the page', () => {
        expect(nextDeparturePhase('disconnecting')).toBe('dark');
        expect(nextDeparturePhase('dark')).toBe('leaving');
    });

    it('stays put until the visitor sets off and once they are gone', () => {
        expect(nextDeparturePhase('here')).toBe('here');
        expect(nextDeparturePhase('leaving')).toBe('leaving');
    });
});
