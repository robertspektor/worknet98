import { useEffect, useRef } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { APPS } from '../apps/app-registry';
import type { AppId } from '../apps/app-registry';
import { useAppTitle } from '../apps/use-app-title';
import { useEdition } from '../computer/edition-context';
import type { IconName } from '../ui/pixel-art';
import { PixelIcon } from '../ui/pixel-icon';

function MenuItem({
    icon,
    label,
    onSelect,
}: {
    icon: IconName;
    label: string;
    onSelect: () => void;
}) {
    return (
        <li>
            <button
                type="button"
                className="start-menu-item"
                onClick={onSelect}
            >
                <PixelIcon name={icon} size={24} />
                <span>{label}</span>
            </button>
        </li>
    );
}

export function StartMenu({
    onOpenApp,
    onLogOff,
    onShutDown,
    onClose,
}: {
    onOpenApp: (id: AppId) => void;
    onLogOff: () => void;
    onShutDown: () => void;
    onClose: () => void;
}) {
    const { t } = useTranslation();
    const appTitle = useAppTitle();
    const { apps } = useEdition();
    const menuRef = useRef<HTMLElement>(null);

    useEffect(() => {
        const closeOnOutsidePointer = (event: PointerEvent) => {
            const target = event.target as HTMLElement;

            if (
                !menuRef.current?.contains(target) &&
                !target.closest('.start-button')
            ) {
                onClose();
            }
        };
        document.addEventListener('pointerdown', closeOnOutsidePointer);

        return () =>
            document.removeEventListener('pointerdown', closeOnOutsidePointer);
    }, [onClose]);

    const select = (action: () => void) => () => {
        onClose();
        action();
    };

    return (
        <nav ref={menuRef} className="start-menu">
            <div className="start-menu-side">
                <span>
                    Desk<b>OS</b> 98
                </span>
            </div>
            <ul className="start-menu-items">
                {apps.map((id) => (
                    <MenuItem
                        key={id}
                        icon={APPS[id].icon}
                        label={appTitle(id)}
                        onSelect={select(() => onOpenApp(id))}
                    />
                ))}
                <li className="start-menu-separator" role="separator" />
                <MenuItem
                    icon="key"
                    label={t('start_menu.log_off')}
                    onSelect={select(onLogOff)}
                />
                <MenuItem
                    icon="computer"
                    label={t('start_menu.shut_down')}
                    onSelect={select(onShutDown)}
                />
            </ul>
        </nav>
    );
}
