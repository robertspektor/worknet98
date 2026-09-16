import type { ReactNode } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import type { IconName } from '../ui/pixel-art';
import { PixelGlyph, PixelIcon } from '../ui/pixel-icon';
import { useWindowDrag } from './use-window-drag';
import type { WindowEntry } from './window-state';

export function WindowFrame({
    entry,
    title,
    icon,
    isFocused,
    onFocus,
    onMinimize,
    onClose,
    onMove,
    children,
}: {
    entry: WindowEntry;
    title: string;
    icon: IconName;
    isFocused: boolean;
    onFocus: () => void;
    onMinimize: () => void;
    onClose: () => void;
    onMove: (position: WindowEntry['position']) => void;
    children: ReactNode;
}) {
    const { t } = useTranslation();
    const startDrag = useWindowDrag(entry.position, entry.size.width, onMove);

    return (
        <section
            className={`window ${isFocused ? 'is-focused' : ''}`}
            aria-label={title}
            hidden={entry.minimized}
            style={{
                left: entry.position.x,
                top: entry.position.y,
                width: entry.size.width,
                height: entry.size.height,
                zIndex: entry.zIndex,
            }}
            onPointerDown={() => !isFocused && onFocus()}
        >
            <header className="title-bar" onPointerDown={startDrag}>
                <span className="title-bar-text">
                    <PixelIcon name={icon} size={16} />
                    <span>{title}</span>
                </span>
                <button
                    type="button"
                    className="title-button"
                    aria-label={t('window.minimize')}
                    onClick={onMinimize}
                >
                    <PixelGlyph name="minimize" />
                </button>
                <button
                    type="button"
                    className="title-button"
                    data-window-action="close"
                    aria-label={t('window.close')}
                    onClick={onClose}
                >
                    <PixelGlyph name="close" />
                </button>
            </header>
            <div className="window-body">{children}</div>
        </section>
    );
}
