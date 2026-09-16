import { useTranslation } from '@/i18n/use-translation';
import { APPS } from '../apps/app-registry';
import type { AppId } from '../apps/app-registry';
import { useWindowManager } from './window-manager';
import { WindowFrame } from './window-frame';

export function WindowLayer() {
    const { t } = useTranslation();
    const windows = useWindowManager();

    return (
        <div className="window-layer">
            {windows.state.windows.map((entry) => {
                const app = APPS[entry.id as AppId];
                const App = app.component;

                return (
                    <WindowFrame
                        key={entry.id}
                        entry={entry}
                        title={t(app.titleKey)}
                        icon={app.icon}
                        isFocused={windows.focusedId === entry.id}
                        onFocus={() => windows.focus(entry.id)}
                        onMinimize={() => windows.minimize(entry.id)}
                        onClose={() => windows.close(entry.id)}
                        onMove={(position) => windows.move(entry.id, position)}
                    >
                        <App />
                    </WindowFrame>
                );
            })}
        </div>
    );
}
