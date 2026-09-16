import type { FloppyDisk } from '@/types';
import type { IconName } from '../../ui/pixel-art';

export type DiskFile = {
    name: string;
    icon: IconName;
    action: 'read' | 'setup';
};

const README: DiskFile = {
    name: 'README.TXT',
    icon: 'text-file',
    action: 'read',
};
const SETUP: DiskFile = { name: 'SETUP.EXE', icon: 'program', action: 'setup' };

export function filesOn(disk: FloppyDisk): DiskFile[] {
    return disk.program ? [README, SETUP] : [README];
}
