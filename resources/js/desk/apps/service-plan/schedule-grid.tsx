import { useTranslation } from '@/i18n/use-translation';
import type { Schedule } from '@/types';
import { slotState } from './slot-state';

export type SelectedSlot = { technicianId: number; slot: string };

export function ScheduleGrid({
    schedule,
    day,
    selected,
    onSelect,
}: {
    schedule: Schedule;
    day: string;
    selected: SelectedSlot | null;
    onSelect: (slot: SelectedSlot) => void;
}) {
    const { t } = useTranslation();

    return (
        <table className="schedule-grid">
            <thead>
                <tr>
                    <th>{t('service_plan.technician')}</th>
                    {schedule.slots.map((slot) => (
                        <th key={slot}>{slot}</th>
                    ))}
                </tr>
            </thead>
            <tbody>
                {schedule.technicians.map((technician) => (
                    <tr key={technician.id}>
                        <th scope="row">
                            <span className="schedule-technician">
                                {technician.name}
                            </span>
                            <span className="schedule-skills">
                                {technician.skills
                                    .map((skill) => t(`skills.${skill}`))
                                    .join(', ')}
                            </span>
                        </th>
                        {schedule.slots.map((slot) => {
                            const state = slotState(
                                schedule,
                                technician.id,
                                day,
                                slot,
                            );
                            const isSelected =
                                selected?.technicianId === technician.id &&
                                selected.slot === slot;

                            return (
                                <td key={slot}>
                                    <button
                                        type="button"
                                        className={`schedule-cell is-${state.kind} ${isSelected ? 'is-selected' : ''}`}
                                        disabled={state.kind === 'busy'}
                                        onClick={() =>
                                            onSelect({
                                                technicianId: technician.id,
                                                slot,
                                            })
                                        }
                                    >
                                        {state.kind === 'busy' &&
                                            t('service_plan.busy')}
                                        {state.kind === 'booked' &&
                                            state.appointment.customer.name}
                                        {state.kind === 'free' &&
                                            t('service_plan.free')}
                                    </button>
                                </td>
                            );
                        })}
                    </tr>
                ))}
            </tbody>
        </table>
    );
}
