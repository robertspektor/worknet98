export type DeparturePhase = 'here' | 'disconnecting' | 'dark' | 'leaving';

export const DEPARTURE_DELAY_MS: Record<DeparturePhase, number> = {
    here: 0,
    disconnecting: 1500,
    dark: 900,
    leaving: 0,
};

export function nextDeparturePhase(phase: DeparturePhase): DeparturePhase {
    if (phase === 'disconnecting') {
        return 'dark';
    }

    return phase === 'dark' ? 'leaving' : phase;
}
