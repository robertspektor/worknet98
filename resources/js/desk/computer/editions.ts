import { APP_IDS } from '../apps/app-registry';
import type { AppId } from '../apps/app-registry';

export type Edition = {
    subtitleKey: string;
    bootStatusKeys: string[];
    apps: AppId[];
};

export const HOME_EDITION: Edition = {
    subtitleKey: 'splash.subtitle',
    bootStatusKeys: [
        'splash.status_1',
        'splash.status_2',
        'splash.status_3',
        'splash.status_4',
    ],
    apps: APP_IDS,
};

export const BUSINESS_EDITION: Edition = {
    subtitleKey: 'splash.business_subtitle',
    bootStatusKeys: [
        'splash.business_status_1',
        'splash.business_status_2',
        'splash.business_status_3',
        'splash.status_4',
    ],
    apps: ['recycle-bin'],
};
