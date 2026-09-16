import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { APPS } from '../apps/app-registry';
import type { AppId } from '../apps/app-registry';
import { sound } from '../sound/sound';
import { PixelIcon } from '../ui/pixel-icon';
import { useWindowManager } from '../windows/window-manager';
import { StartMenu } from './start-menu';
import { TrayClock } from './tray-clock';
import { TrayMail } from './tray-mail';

export function Taskbar({
    onLogOff,
    onShutDown,
}: {
    onLogOff: () => void;
    onShutDown: () => void;
}) {
    const { t } = useTranslation();
    const windows = useWindowManager();
    const [isStartMenuOpen, setStartMenuOpen] = useState(false);

    const toggleTask = (id: string) =>
        windows.focusedId === id ? windows.minimize(id) : windows.focus(id);

    return (
        <>
            {isStartMenuOpen && (
                <StartMenu
                    onOpenApp={windows.open}
                    onLogOff={onLogOff}
                    onShutDown={onShutDown}
                    onClose={() => setStartMenuOpen(false)}
                />
            )}
            <div className="taskbar">
                <button
                    type="button"
                    className={`button start-button ${isStartMenuOpen ? 'is-pressed' : ''}`}
                    onClick={() => {
                        sound.click();
                        setStartMenuOpen(!isStartMenuOpen);
                    }}
                >
                    <PixelIcon name="logo" size={16} />
                    <span>{t('taskbar.start')}</span>
                </button>
                <div className="task-buttons">
                    {windows.state.windows.map((entry) => {
                        const app = APPS[entry.id as AppId];

                        return (
                            <button
                                key={entry.id}
                                type="button"
                                className={`button task-button ${windows.focusedId === entry.id ? 'is-pressed' : ''}`}
                                onClick={() => toggleTask(entry.id)}
                            >
                                <PixelIcon name={app.icon} size={16} />
                                <span>{t(app.titleKey)}</span>
                            </button>
                        );
                    })}
                </div>
                <div className="tray">
                    <TrayMail />
                    <TrayClock />
                </div>
            </div>
        </>
    );
}
