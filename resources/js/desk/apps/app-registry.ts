import type { ComponentType } from 'react';
import type { SoftwareType } from '@/types';
import type { IconName } from '../ui/pixel-art';
import type { Size } from '../windows/window-state';
import { BrowserApp } from './browser/browser-app';
import { CalculatorApp } from './calculator/calculator-app';
import { CalendarApp } from './calendar/calendar-app';
import { ControlPanelApp } from './control-panel-app';
import { CustomerBaseApp } from './customer-base/customer-base-app';
import { FloppyDriveApp } from './floppy-drive/floppy-drive-app';
import { MailApp } from './mail/mail-app';
import { MessengerApp } from './messenger/messenger-app';
import { MinefieldApp } from './minefield/minefield-app';
import { MyComputerApp } from './my-computer-app';
import { NotepadApp } from './notepad/notepad-app';
import { RecycleBinApp } from './recycle-bin-app';
import { ServicePlanApp } from './service-plan/service-plan-app';
import { TimeClockApp } from './time-clock/time-clock-app';
import { WorkNetApp } from './worknet/worknet-app';

export type AppDefinition = {
    titleKey: string;
    icon: IconName;
    size: Size;
    component: ComponentType;
    softwareType?: SoftwareType;
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
    messenger: {
        titleKey: 'desktop.messenger',
        icon: 'messenger',
        size: { width: 470, height: 340 },
        component: MessengerApp,
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
    records: {
        titleKey: 'desktop.records',
        icon: 'records',
        size: { width: 560, height: 380 },
        component: CustomerBaseApp,
        softwareType: 'records',
    },
    scheduler: {
        titleKey: 'desktop.scheduler',
        icon: 'scheduler',
        size: { width: 660, height: 332 },
        component: ServicePlanApp,
        softwareType: 'scheduler',
    },
    calendar: {
        titleKey: 'desktop.calendar',
        icon: 'calendar',
        size: { width: 420, height: 400 },
        component: CalendarApp,
    },
    'time-clock': {
        titleKey: 'desktop.time_clock',
        icon: 'clock',
        size: { width: 360, height: 346 },
        component: TimeClockApp,
    },
    'floppy-drive': {
        titleKey: 'desktop.floppy_drive',
        icon: 'floppy',
        size: { width: 380, height: 300 },
        component: FloppyDriveApp,
    },
    minefield: {
        titleKey: 'desktop.minefield',
        icon: 'mine',
        size: { width: 236, height: 330 },
        component: MinefieldApp,
    },
    browser: {
        titleKey: 'desktop.browser',
        icon: 'browser',
        size: { width: 620, height: 460 },
        component: BrowserApp,
    },
    calculator: {
        titleKey: 'desktop.calculator',
        icon: 'calculator',
        size: { width: 220, height: 280 },
        component: CalculatorApp,
    },
    notepad: {
        titleKey: 'desktop.notepad',
        icon: 'notepad',
        size: { width: 420, height: 320 },
        component: NotepadApp,
    },
} satisfies Record<string, AppDefinition>;

export type AppId = keyof typeof APPS;

export const APP_IDS = Object.keys(APPS) as AppId[];
