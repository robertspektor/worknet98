import type { RefObject } from 'react';
import { useLayoutEffect, useRef, useState } from 'react';
import { shiftIntoView } from './viewport-fit';

const GUTTER_PX = 16;

export function useKeepInView<T extends HTMLElement>(): {
    ref: RefObject<T | null>;
    shift: number;
} {
    const ref = useRef<T>(null);
    const [shift, setShift] = useState(0);

    useLayoutEffect(() => {
        const box = ref.current?.getBoundingClientRect();

        if (box) {
            setShift(shiftIntoView(box, window.innerWidth, GUTTER_PX));
        }
    }, []);

    return { ref, shift };
}
