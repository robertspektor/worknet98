import type { DiskFile } from '@/types';
import type { IconName } from '../../ui/pixel-art';

export function fileIcon(file: DiskFile): IconName {
    return file.kind === 'setup' ? 'program' : 'text-file';
}
