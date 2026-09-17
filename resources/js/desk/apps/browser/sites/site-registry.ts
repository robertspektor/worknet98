import type { ComponentType } from 'react';
import { DiskDepotSite } from './disk-depot-site';
import { StartPage } from './start-page';

export type SiteProps = { onOpen: (site: SiteId) => void };

type SiteDefinition = {
    address: string;
    component: ComponentType<SiteProps>;
};

export const SITES = {
    start: { address: 'http://www.welcome.wn/', component: StartPage },
    diskdepot: {
        address: 'http://www.diskdepot.wn/',
        component: DiskDepotSite,
    },
} satisfies Record<string, SiteDefinition>;

export type SiteId = keyof typeof SITES;
