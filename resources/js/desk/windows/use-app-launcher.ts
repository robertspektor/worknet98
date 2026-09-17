import { useEffect, useRef, useState } from 'react';
import type { AppId } from '../apps/app-registry';
import { useEdition } from '../computer/edition-context';
import { appLaunchDelayMs } from '../hardware/performance';
import { sound } from '../sound/sound';

export function useAppLauncher(
    isOpen: (id: AppId) => boolean,
    openNow: (id: AppId) => void,
) {
    const { cpu } = useEdition();
    const delayMs = appLaunchDelayMs(cpu.speedMhz);
    const [launchingId, setLaunchingId] = useState<AppId | null>(null);
    const openNowRef = useRef(openNow);

    useEffect(() => {
        openNowRef.current = openNow;
    }, [openNow]);

    useEffect(() => {
        if (launchingId === null) {
            return;
        }

        const timer = setTimeout(() => {
            openNowRef.current(launchingId);
            setLaunchingId(null);
        }, delayMs);

        return () => clearTimeout(timer);
    }, [launchingId, delayMs]);

    const launch = (id: AppId) => {
        if (launchingId !== null) {
            return;
        }

        if (delayMs === 0 || isOpen(id)) {
            openNow(id);

            return;
        }

        sound.hddSeek();
        setLaunchingId(id);
    };

    return { launch, isLaunching: launchingId !== null };
}
