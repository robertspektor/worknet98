import { useEffect, useRef } from 'react';

export const OVERHEAT_AFTER_MS = 90_000;

export function useOverheating(isRunningHot: boolean, onOverheat: () => void) {
    const onOverheatRef = useRef(onOverheat);

    useEffect(() => {
        onOverheatRef.current = onOverheat;
    }, [onOverheat]);

    useEffect(() => {
        if (!isRunningHot) {
            return;
        }

        const timer = setTimeout(
            () => onOverheatRef.current(),
            OVERHEAT_AFTER_MS,
        );

        return () => clearTimeout(timer);
    }, [isRunningHot]);
}
