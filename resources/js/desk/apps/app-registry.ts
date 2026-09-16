import type { ComponentType } from 'react';
import type { IconName } from '../ui/pixel-art';
import type { Size } from '../windows/window-state';
import { ControlPanelApp } from './control-panel-app';
import { FloppyDriveApp } from './floppy-drive/floppy-drive-app';
import { MailApp } from './mail/mail-app';
import { MinefieldApp } from './minefield/minefield-app';
import { MyComputerApp } from './my-computer-app';
import { RecycleBinApp } from './recycle-bin-app';
import { TimeClockApp } from './time-clock/time-clock-app';
import { WorkNetApp } from './worknet/worknet-app';

export type AppDefinition = {
    titleKey: string;
    icon: IconName;
    size: Size;
    component: ComponentType;
};

export const APPS = {
    'my-computer': {
        titleKey: 'desktop.my_computer',
        icon: 'computer',
        size: { width: 380, height: 250 },
        component: MyComputerApp,
    },
    worknet: {
        titleKey: 'desktop.worknet',
        icon: 'worknet',
        size: { width: 600, height: 440 },
        component: WorkNetApp,
    },
    mail: {
        titleKey: 'desktop.mail',
        icon: 'mail',
        size: { width: 560, height: 400 },
        component: MailApp,
    },
    'control-panel': {
        titleKey: 'desktop.control_panel',
        icon: 'control-panel',
        size: { width: 380, height: 200 },
        component: ControlPanelApp,
    },
    'recycle-bin': {
        titleKey: 'desktop.recycle_bin',
        icon: 'trash',
        size: { width: 380, height: 236 },
        component: RecycleBinApp,
    },
    'time-clock': {
        titleKey: 'desktop.time_clock',
        icon: 'clock',
        size: { width: 340, height: 196 },
        component: TimeClockApp,
    },
    'floppy-drive': {
        titleKey: 'desktop.floppy_drive',
        icon: 'floppy',
        size: { width: 360, height: 264 },
        component: FloppyDriveApp,
    },
    minefield: {
        titleKey: 'desktop.minefield',
        icon: 'mine',
        size: { width: 236, height: 330 },
        component: MinefieldApp,
    },
} satisfies Record<string, AppDefinition>;

export type AppId = keyof typeof APPS;

export const APP_IDS = Object.keys(APPS) as AppId[];
