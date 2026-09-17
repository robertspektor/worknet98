import { Head } from '@inertiajs/react';
import { BUSINESS_EDITION } from '@/desk/computer/editions';
import { Workstation } from '@/desk/computer/workstation';
import { AssetTag } from '@/desk/room/asset-tag';
import { CoffeeMug } from '@/desk/room/coffee-mug';
import { HouseKeys } from '@/desk/room/house-keys';
import { InTray } from '@/desk/room/in-tray';
import { PlacementProvider } from '@/desk/placement/placement-provider';
import { ShiftClockProvider } from '@/desk/shift/shift-clock-provider';
import { NamePlate } from '@/desk/room/name-plate';
import { Room } from '@/desk/room/room';
import type { Workplace } from '@/types';

export default function Office({ workplace }: { workplace: Workplace }) {
    return (
        <>
            <Head />
            <PlacementProvider isSignedIn>
                <ShiftClockProvider>
                    <Room scene="office">
                        <Workstation
                            edition={BUSINESS_EDITION}
                            accessory={<AssetTag company={workplace.company} />}
                        />
                        <InTray />
                        <NamePlate title={workplace.jobTitle} />
                        <CoffeeMug company={workplace.company} />
                        <HouseKeys />
                    </Room>
                </ShiftClockProvider>
            </PlacementProvider>
        </>
    );
}
