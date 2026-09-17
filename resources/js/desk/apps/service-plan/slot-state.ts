import type { Appointment, Schedule } from '@/types';

export type SlotState =
    | { kind: 'busy' }
    | { kind: 'booked'; appointment: Appointment }
    | { kind: 'free' };

export function slotState(
    schedule: Schedule,
    technicianId: number,
    day: string,
    slot: string,
): SlotState {
    const appointment = schedule.appointments.find(
        (entry) =>
            entry.technician_id === technicianId &&
            entry.date === day &&
            entry.slot === slot,
    );

    if (appointment) {
        return { kind: 'booked', appointment };
    }

    const technician = schedule.technicians.find(
        (entry) => entry.id === technicianId,
    );

    return technician?.busy.includes(`${day} ${slot}`)
        ? { kind: 'busy' }
        : { kind: 'free' };
}

export function isForeign(state: SlotState): boolean {
    return state.kind === 'booked' && !state.appointment.is_own;
}
