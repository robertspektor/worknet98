import { useTranslation } from '@/i18n/use-translation';
import type { DiskFile, PlayerFloppyDisk } from '@/types';
import { GameApiError } from '../../api/game-api';
import { useDialogs } from '../../dialogs/dialog-provider';
import { useFloppyDrive } from '../../floppy/floppy-drive-provider';
import { sound } from '../../sound/sound';

const LABEL_MAX_LENGTH = 24;

export function useDiskActions() {
    const { t } = useTranslation();
    const dialogs = useDialogs();
    const floppyDrive = useFloppyDrive();

    const reportFailure = async (error: unknown) => {
        sound.error();
        await dialogs.alert({
            title: t('floppy_drive.write_failed_title'),
            message:
                error instanceof GameApiError
                    ? error.message
                    : t('floppy_drive.not_ready'),
            icon: 'warning',
        });
    };

    const label = async (disk: PlayerFloppyDisk) => {
        const text = await dialogs.prompt({
            title: t('floppy_drive.label_title'),
            message: t('floppy_drive.label_prompt'),
            icon: 'floppy',
            cancelLabel: t('dialog.cancel'),
            input: {
                value: disk.label ?? '',
                maxLength: LABEL_MAX_LENGTH,
            },
        });

        if (text !== null) {
            await floppyDrive.label(disk, text).catch(reportFailure);
        }
    };

    const erase = async (file: DiskFile) => {
        const confirmed = await dialogs.confirm({
            title: t('floppy_drive.delete_title'),
            message: t('floppy_drive.delete_confirm', { file: file.name }),
            icon: 'warning',
            confirmLabel: t('floppy_drive.delete'),
            cancelLabel: t('dialog.cancel'),
        });

        if (confirmed) {
            await floppyDrive.eraseFile(file).catch(reportFailure);
        }
    };

    return { label, erase, reportFailure };
}
