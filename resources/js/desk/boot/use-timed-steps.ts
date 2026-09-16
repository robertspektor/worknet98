import { useEffect, useRef, useState } from 'react';

export function useTimedSteps(
    stepCount: number,
    delayMs: number,
    onDone: () => void,
): number {
    const [step, setStep] = useState(1);
    const onDoneRef = useRef(onDone);

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
        }, delayMs);

        return () => clearTimeout(timer);
    }, [step, stepCount, delayMs]);

    return step;
}
