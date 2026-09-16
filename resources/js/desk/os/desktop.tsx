import { router } from '@inertiajs/react';
import { useTranslation } from '@/i18n/use-translation';
import { logout } from '@/routes';
import { DialogProvider, useDialogs } from '../dialogs/dialog-provider';
import { MailboxProvider } from '../mailbox/mailbox-provider';
import { WindowLayer } from '../windows/window-layer';
import { WindowManagerProvider } from '../windows/window-manager';
import { DesktopIcons } from './desktop-icons';
import { FloppyAutoplay } from './floppy-autoplay';
import { Taskbar } from './taskbar';

function DesktopSurface({ onShutDown }: { onShutDown: () => void }) {
    const { t } = useTranslation();
    const dialogs = useDialogs();

    const logOff = async () => {
        const confirmed = await dialogs.confirm({
            title: t('log_off.title'),
            message: t('log_off.message'),
            icon: 'key',
            confirmLabel: t('log_off.confirm'),
            cancelLabel: t('dialog.cancel'),
        });

        if (confirmed) {
            router.post(logout.url(), {}, { preserveState: true });
        }
    };

    return (
        <>
            <FloppyAutoplay />
            <DesktopIcons />
            <WindowLayer />
            <Taskbar onLogOff={() => void logOff()} onShutDown={onShutDown} />
        </>
    );
}

export function Desktop({ onShutDown }: { onShutDown: () => void }) {
    return (
        <div className="os">
            <WindowManagerProvider>
                <MailboxProvider>
                    <DialogProvider>
                        <DesktopSurface onShutDown={onShutDown} />
                    </DialogProvider>
                </MailboxProvider>
            </WindowManagerProvider>
        </div>
    );
}
