export type Operator = '+' | '-' | '*' | '/';

export type Calculator = {
    display: string;
    stored: number | null;
    operator: Operator | null;
    startsNewNumber: boolean;
    hasError: boolean;
};

export type CalculatorKey =
    | { type: 'digit'; digit: string }
    | { type: 'decimal' }
    | { type: 'operator'; operator: Operator }
    | { type: 'equals' }
    | { type: 'clear' };

const MAX_DIGITS = 12;

export const initialCalculator: Calculator = {
    display: '0',
    stored: null,
    operator: null,
    startsNewNumber: true,
    hasError: false,
};

export function press(calculator: Calculator, key: CalculatorKey): Calculator {
    if (key.type === 'clear') {
        return initialCalculator;
    }

    if (calculator.hasError) {
        return calculator;
    }

    switch (key.type) {
        case 'digit':
            return enterDigit(calculator, key.digit);
        case 'decimal':
            return enterDecimal(calculator);
        case 'operator':
            return chooseOperator(calculator, key.operator);
        case 'equals':
            return equals(calculator);
    }
}

function enterDigit(calculator: Calculator, digit: string): Calculator {
    if (calculator.startsNewNumber) {
        return { ...calculator, display: digit, startsNewNumber: false };
    }

    if (digitCount(calculator.display) >= MAX_DIGITS) {
        return calculator;
    }

    const display =
        calculator.display === '0' ? digit : calculator.display + digit;

    return { ...calculator, display };
}

function enterDecimal(calculator: Calculator): Calculator {
    if (calculator.startsNewNumber) {
        return { ...calculator, display: '0.', startsNewNumber: false };
    }

    return calculator.display.includes('.')
        ? calculator
        : { ...calculator, display: `${calculator.display}.` };
}

function chooseOperator(
    calculator: Calculator,
    operator: Operator,
): Calculator {
    const settled = calculator.startsNewNumber
        ? calculator
        : equals(calculator);

    if (settled.hasError) {
        return settled;
    }

    return {
        ...settled,
        stored: Number(settled.display),
        operator,
        startsNewNumber: true,
    };
}

function equals(calculator: Calculator): Calculator {
    if (calculator.operator === null || calculator.stored === null) {
        return { ...calculator, startsNewNumber: true };
    }

    const result = calculate(
        calculator.stored,
        calculator.operator,
        Number(calculator.display),
    );

    if (!Number.isFinite(result)) {
        return { ...initialCalculator, display: 'Error', hasError: true };
    }

    return {
        display: format(result),
        stored: null,
        operator: null,
        startsNewNumber: true,
        hasError: false,
    };
}

function calculate(left: number, operator: Operator, right: number): number {
    switch (operator) {
        case '+':
            return left + right;
        case '-':
            return left - right;
        case '*':
            return left * right;
        case '/':
            return left / right;
    }
}

function format(value: number): string {
    const rounded = Number(value.toPrecision(MAX_DIGITS));

    return digitCount(String(rounded)) > MAX_DIGITS
        ? rounded.toExponential(6)
        : String(rounded);
}

function digitCount(display: string): number {
    return display.replace(/[^0-9]/g, '').length;
}
