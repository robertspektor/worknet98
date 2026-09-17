export type HardwareSlot = 'cpu';

export type HardwarePart = {
    id: number;
    slug: string;
    slot: HardwareSlot;
    speed_mhz: number;
};

export type InstalledCpu = {
    slug: string;
    speed_mhz: number;
    needs_thermal_paste: boolean;
};

export type DeskPart = {
    id: number;
    slug: string;
    slot: HardwareSlot;
    speed_mhz: number;
    is_used: boolean;
};

export type HomeComputer = { cpu: InstalledCpu; desk_parts: DeskPart[] };
