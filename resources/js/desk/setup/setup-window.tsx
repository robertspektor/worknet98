import type { ReactNode } from 'react';
import type { IconName } from '../ui/pixel-art';
import { PixelIcon } from '../ui/pixel-icon';

export function SetupWindow({
    title,
    icon,
    children,
}: {
    title: string;
    icon: IconName;
    children: ReactNode;
}) {
    return (
        <div className="setup-screen os">
            <section
                className="window setup-window is-focused"
                aria-label={title}
            >
                <header className="title-bar">
                    <span className="title-bar-text">
                        <PixelIcon name="logo" size={16} />
                        <span>{title}</span>
                    </span>
                </header>
                <div className="setup-body">
                    <PixelIcon name={icon} size={48} />
                    <div>{children}</div>
                </div>
            </section>
        </div>
    );
}
