import { useTranslation } from '@/i18n/use-translation';
import { GameApiError } from '../../api/game-api';
import { useDialogs } from '../../dialogs/dialog-provider';
import { toTextFileName } from '../../floppy/file-name';
import { useOptionalFloppyDrive } from '../../floppy/floppy-drive-provider';
import { sound } from '../../sound/sound';

const FILE_NAME_MAX_LENGTH = 12;
const DEFAULT_FILE_NAME = 'NOTE.TXT';

export function useSaveToDisk() {
    const { t } = useTranslation();
    const dialogs = useDialogs();
    const floppyDrive = useOptionalFloppyDrive();

    const warn = async (message: string) => {
        sound.error();
        await dialogs.alert({
            title: t('notepad.save_title'),
            message,
            icon: 'warning',
        });
    };

    const askFileName = () =>
        dialogs.prompt({
            title: t('notepad.save_title'),
            message: t('notepad.file_name_prompt'),
            icon: 'floppy',
            confirmLabel: t('notepad.save'),
            cancelLabel: t('dialog.cancel'),
            input: {
                value: DEFAULT_FILE_NAME,
                maxLength: FILE_NAME_MAX_LENGTH,
            },
        });

    const confirmReplace = (name: string) =>
        dialogs.confirm({
            title: t('notepad.save_title'),
            message: t('notepad.replace_confirm', { file: name }),
            icon: 'warning',
            confirmLabel: t('notepad.replace'),
            cancelLabel: t('dialog.cancel'),
        });

    const save = async (body: string) => {
        const files = floppyDrive?.loadedFiles;

        if (!floppyDrive?.loadedDisk || !files) {
            return warn(t('floppy_drive.not_ready'));
        }

        if (floppyDrive.loadedDisk.is_write_protected) {
            return warn(t('floppy_disk.refusal.write_protected'));
        }

        const input = await askFileName();

        if (input === null) {
            return;
        }

        const name = toTextFileName(input);

        if (!name) {
            return warn(t('notepad.invalid_name'));
        }

        if (
            files.some((file) => file.name === name) &&
            !(await confirmReplace(name))
        ) {
            return;
        }

        try {
            sound.floppySeek();
            await floppyDrive.saveFile(name, body);
            await dialogs.alert({
                title: t('notepad.save_title'),
                message: t('notepad.saved_to_disk', { file: name }),
                icon: 'info',
            });
        } catch (error) {
            await warn(
                error instanceof GameApiError
                    ? error.message
                    : t('floppy_drive.not_ready'),
            );
        }
    };

    return { canSave: floppyDrive !== null, save };
}
