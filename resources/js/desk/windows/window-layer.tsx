import { APPS } from '../apps/app-registry';
import type { AppId } from '../apps/app-registry';
import { useAppTitle } from '../apps/use-app-title';
import { useWindowManager } from './window-manager';
import { WindowFrame } from './window-frame';

export function WindowLayer() {
    const appTitle = useAppTitle();
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
                        title={appTitle(entry.id as AppId)}
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
