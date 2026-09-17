import type { RefObject } from 'react';
import { useEffect, useRef } from 'react';

const INPUT_EVENTS = ['pointerdown', 'pointermove', 'keydown', 'wheel'];

export function useLastInput(): RefObject<number> {
    const lastInput = useRef(Date.now());

    useEffect(() => {
        const record = () => {
            lastInput.current = Date.now();
        };
        INPUT_EVENTS.forEach((type) =>
            window.addEventListener(type, record, { passive: true }),
        );

        return () =>
            INPUT_EVENTS.forEach((type) =>
                window.removeEventListener(type, record),
            );
    }, []);

    return lastInput;
}
