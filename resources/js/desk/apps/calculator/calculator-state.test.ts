import { describe, expect, it } from 'vite-plus/test';
import type { Calculator, CalculatorKey } from './calculator-state';
import { initialCalculator, press } from './calculator-state';

function type(keys: string): Calculator {
    return keys
        .split('')
        .reduce(
            (calculator, character) => press(calculator, keyFor(character)),
            initialCalculator,
        );
}

function keyFor(character: string): CalculatorKey {
    if (character === '.') {
        return { type: 'decimal' };
    }

    if (character === '=') {
        return { type: 'equals' };
    }

    if (character === 'C') {
        return { type: 'clear' };
    }

    return '+-*/'.includes(character)
        ? { type: 'operator', operator: character as '+' }
        : { type: 'digit', digit: character };
}

describe('calculator', () => {
    it('shows zero on start', () => {
        expect(initialCalculator.display).toBe('0');
    });

    it('enters numbers with a decimal point', () => {
        expect(type('012.50').display).toBe('12.50');
        expect(type('.5').display).toBe('0.5');
        expect(type('1..2').display).toBe('1.2');
    });

    it('calculates with each operator', () => {
        expect(type('12+30=').display).toBe('42');
        expect(type('5-8=').display).toBe('-3');
        expect(type('6*7=').display).toBe('42');
        expect(type('10/4=').display).toBe('2.5');
    });

    it('chains operations from left to right', () => {
        expect(type('2+3*4=').display).toBe('20');
    });

    it('shows the running result when chaining', () => {
        expect(type('2+3*').display).toBe('5');
    });

    it('replaces an operator pressed twice', () => {
        expect(type('9+-4=').display).toBe('5');
    });

    it('starts a new number after a result', () => {
        expect(type('1+1=7').display).toBe('7');
    });

    it('hides floating point noise', () => {
        expect(type('.1+.2=').display).toBe('0.3');
    });

    it('shows an error when dividing by zero until cleared', () => {
        const error = type('1/0=');

        expect(error.display).toBe('Error');
        expect(press(error, { type: 'digit', digit: '3' })).toBe(error);
        expect(press(error, { type: 'clear' })).toEqual(initialCalculator);
    });

    it('limits the number of digits', () => {
        expect(type('1234567890123').display).toBe('123456789012');
    });
});
