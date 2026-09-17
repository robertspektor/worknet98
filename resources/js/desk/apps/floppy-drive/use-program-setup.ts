import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import type { DiskFile } from '@/types';
import { useDialogs } from '../../dialogs/dialog-provider';
import { useFloppyDrive } from '../../floppy/floppy-drive-provider';
import { installedApps } from '../../floppy/installed-apps';
import { sound } from '../../sound/sound';
import { APPS } from '../app-registry';

export const SETUP_DURATION_MS = 2400;

const wait = (ms: number) => new Promise((resolve) => setTimeout(resolve, ms));

export function useProgramSetup() {
    const { t } = useTranslation();
    const dialogs = useDialogs();
    const floppyDrive = useFloppyDrive();
    const [isInstalling, setInstalling] = useState(false);

    const copyFiles = async (setupFile: DiskFile) => {
        setInstalling(true);
        sound.floppySeek();

        try {
            await Promise.all([
                floppyDrive.install(setupFile),
                wait(SETUP_DURATION_MS),
            ]);
        } finally {
            setInstalling(false);
        }
    };

    const install = async (setupFile: DiskFile) => {
        const [app] = installedApps([setupFile.program ?? '']);

        if (!app) {
            return;
        }

        const program = t(APPS[app].titleKey);

        if (floppyDrive.installedPrograms.includes(app)) {
            await dialogs.alert({
                title: t('program_setup.title'),
                message: t('program_setup.already_installed', { program }),
                icon: 'info',
            });

            return;
        }

        const confirmed = await dialogs.confirm({
            title: t('program_setup.title'),
            message: t('program_setup.confirm', { program }),
            icon: 'program',
            confirmLabel: t('program_setup.install'),
            cancelLabel: t('dialog.cancel'),
        });

        if (!confirmed) {
            return;
        }

        try {
            await copyFiles(setupFile);
            await dialogs.alert({
                title: t('program_setup.title'),
                message: t('program_setup.done', { program }),
                icon: 'info',
            });
        } catch {
            sound.error();
            await dialogs.alert({
                title: t('program_setup.title'),
                message: t('program_setup.failed'),
                icon: 'warning',
            });
        }
    };

    return { isInstalling, install };
}
