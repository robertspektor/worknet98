import { useEffect, useRef, useState } from 'react';

export function useTimedSteps(
    stepCount: number,
    delayMs: number | ((step: number) => number),
    onDone: () => void,
): number {
    const [step, setStep] = useState(1);
    const onDoneRef = useRef(onDone);
    const delay = typeof delayMs === 'number' ? delayMs : delayMs(step);

    useEffect(() => {
        onDoneRef.current = onDone;
    }, [onDone]);

    useEffect(() => {
        const timer = setTimeout(() => {
            if (step < stepCount) {
                setStep(step + 1);
            } else {
                onDoneRef.current();
            }
        }, delay);

        return () => clearTimeout(timer);
    }, [step, stepCount, delay]);

    return step;
}
