import { useEffect } from 'react';

function isTyping(target: EventTarget | null): boolean {
    return (
        target instanceof HTMLInputElement ||
        target instanceof HTMLSelectElement ||
        target instanceof HTMLTextAreaElement
    );
}

/* A terminal is worked with function keys, so the screen listens for them
   as long as nobody is filling in a field. */

export function useHotkeys(keys: Record<string, () => void>) {
    useEffect(() => {
        const onKeyDown = (event: KeyboardEvent) => {
            const press = keys[event.key];

            if (press === undefined || isTyping(event.target)) {
                return;
            }

            event.preventDefault();
            press();
        };

        window.addEventListener('keydown', onKeyDown);

        return () => window.removeEventListener('keydown', onKeyDown);
    });
}
