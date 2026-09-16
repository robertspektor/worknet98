import type { ComponentProps } from 'react';
import { useFloppyDrive } from '../floppy/floppy-drive-provider';
import { HOME_EDITION } from './editions';
import { Workstation } from './workstation';

export function HomeWorkstation(
    props: Omit<ComponentProps<typeof Workstation>, 'edition'>,
) {
    const { installedPrograms } = useFloppyDrive();
    const edition = {
        ...HOME_EDITION,
        apps: [...HOME_EDITION.apps, ...installedPrograms],
    };

    return <Workstation edition={edition} {...props} />;
}
