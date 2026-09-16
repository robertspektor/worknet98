import { useEffect, useRef, useState } from 'react';
import { VIEWPORT } from './viewport';

export function useViewportScale() {
    const screenRef = useRef<HTMLDivElement>(null);
    const [scale, setScale] = useState(1);

    useEffect(() => {
        const screen = screenRef.current;

        if (!screen) {
            return;
        }

        const observer = new ResizeObserver(() =>
            setScale(screen.clientWidth / VIEWPORT.width),
        );
        observer.observe(screen);

        return () => observer.disconnect();
    }, []);

    return { screenRef, scale };
}
