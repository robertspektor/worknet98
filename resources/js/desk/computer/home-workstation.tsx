import type { ComponentProps } from 'react';
import { useFloppyDrive } from '../floppy/floppy-drive-provider';
import { useHomeComputer } from '../hardware/home-computer-provider';
import { HOME_EDITION } from './editions';
import { Workstation } from './workstation';

export function HomeWorkstation(
    props: Omit<ComponentProps<typeof Workstation>, 'edition'>,
) {
    const { installedPrograms } = useFloppyDrive();
    const { homeComputer } = useHomeComputer();
    const edition = {
        ...HOME_EDITION,
        cpu: homeComputer
            ? {
                  slug: homeComputer.cpu.slug,
                  speedMhz: homeComputer.cpu.speed_mhz,
              }
            : HOME_EDITION.cpu,
        apps: [...HOME_EDITION.apps, ...installedPrograms],
    };

    return <Workstation edition={edition} {...props} />;
}
