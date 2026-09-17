export type InstalledCpu = {
    slug: string;
    speed_mhz: number;
    needs_thermal_paste: boolean;
};

export type HomeComputer = { cpu: InstalledCpu };
