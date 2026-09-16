import { useState } from 'react';
import { APPS } from '../apps/app-registry';
import type { AppId } from '../apps/app-registry';
import { useAppTitle } from '../apps/use-app-title';
import { useEdition } from '../computer/edition-context';
import { PixelIcon } from '../ui/pixel-icon';
import { useWindowManager } from '../windows/window-manager';

export function DesktopIcons() {
    const appTitle = useAppTitle();
    const { apps } = useEdition();
    const windows = useWindowManager();
    const [selected, setSelected] = useState<AppId | null>(null);

    return (
        <div className="desktop-icons">
            {apps.map((id) => (
                <button
                    key={id}
                    type="button"
                    className={`desktop-icon ${selected === id ? 'is-selected' : ''}`}
                    onPointerDown={() => setSelected(id)}
                    onDoubleClick={() => windows.open(id)}
                    onPointerUp={(event) =>
                        event.pointerType === 'touch' && windows.open(id)
                    }
                    onKeyDown={(event) =>
                        event.key === 'Enter' && windows.open(id)
                    }
                >
                    <PixelIcon name={APPS[id].icon} />
                    <span className="desktop-icon-label">{appTitle(id)}</span>
                </button>
            ))}
        </div>
    );
}
