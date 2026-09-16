import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import type { CalculatorKey } from './calculator-state';
import { initialCalculator, press } from './calculator-state';

type Button = { label: string; key: CalculatorKey; isOperator?: boolean };

const digit = (value: string): Button => ({
    label: value,
    key: { type: 'digit', digit: value },
});

const BUTTONS: Button[] = [
    digit('7'),
    digit('8'),
    digit('9'),
    { label: '/', key: { type: 'operator', operator: '/' }, isOperator: true },
    digit('4'),
    digit('5'),
    digit('6'),
    { label: '*', key: { type: 'operator', operator: '*' }, isOperator: true },
    digit('1'),
    digit('2'),
    digit('3'),
    { label: '-', key: { type: 'operator', operator: '-' }, isOperator: true },
    digit('0'),
    { label: '.', key: { type: 'decimal' } },
    { label: '=', key: { type: 'equals' }, isOperator: true },
    { label: '+', key: { type: 'operator', operator: '+' }, isOperator: true },
];

export function CalculatorApp() {
    const { t } = useTranslation();
    const [calculator, setCalculator] = useState(initialCalculator);
    const enter = (key: CalculatorKey) => setCalculator(press(calculator, key));

    return (
        <div className="calculator">
            <output className="calculator-display sunken">
                {calculator.hasError
                    ? t('calculator.error')
                    : calculator.display}
            </output>
            <button
                type="button"
                className="button calculator-clear"
                onClick={() => enter({ type: 'clear' })}
            >
                {t('calculator.clear')}
            </button>
            <div className="calculator-keys">
                {BUTTONS.map((button) => (
                    <button
                        key={button.label}
                        type="button"
                        className={`button calculator-key ${button.isOperator ? 'is-operator' : ''}`}
                        onClick={() => enter(button.key)}
                    >
                        {button.label}
                    </button>
                ))}
            </div>
        </div>
    );
}
