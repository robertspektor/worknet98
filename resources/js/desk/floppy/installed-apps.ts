import { APPS } from '../apps/app-registry';
import type { AppId } from '../apps/app-registry';

export function installedApps(programs: string[]): AppId[] {
    return programs.filter((program): program is AppId => program in APPS);
}
