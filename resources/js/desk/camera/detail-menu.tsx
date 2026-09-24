import type { ComponentType } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { CalendarMenu } from './calendar-menu';
import { DiskBoxMenu } from './disk-box-menu';
import type { FocusTarget } from './camera-state';
import { TowerMenu } from './tower-menu';

/* What a thing can do while the camera is on it. The monitor has no menu:
   in front of the screen the player works with the machine, not with the
   room. */

const MENUS: Partial<Record<FocusTarget, ComponentType>> = {
    tower: TowerMenu,
    calendar: CalendarMenu,
    'disk-box': DiskBoxMenu,
};

export function MenuAction({
    label,
    isDisabled,
    onSelect,
}: {
    label: string;
    isDisabled?: boolean;
    onSelect: () => void;
}) {
    return (
        <button
            type="button"
            className="detail-action"
            disabled={isDisabled}
            onClick={onSelect}
        >
            {label}
        </button>
    );
}

export function MenuNote({ text }: { text: string }) {
    return <span className="detail-note">{text}</span>;
}

export function DetailMenu({ target }: { target: FocusTarget }) {
    const { t } = useTranslation();
    const Menu = MENUS[target];

    if (!Menu) {
        return null;
    }

    return (
        <div className={`detail-menu is-${target}`}>
            <span className="detail-menu-title">{t(`detail.${target}`)}</span>
            <Menu />
        </div>
    );
}
