import { useEffect, useRef } from 'react';
import { useOptionalFloppyDrive } from '../floppy/floppy-drive-provider';
import { useWindowManager } from '../windows/window-manager';

export function FloppyAutoplay() {
    const floppyDrive = useOptionalFloppyDrive();
    const windows = useWindowManager();
    const loadedId = floppyDrive?.loadedDisk?.id ?? null;
    const previousId = useRef(loadedId);

    useEffect(() => {
        if (loadedId !== null && loadedId !== previousId.current) {
            windows.open('floppy-drive');
        }
        previousId.current = loadedId;
    }, [loadedId, windows]);

    return null;
}
