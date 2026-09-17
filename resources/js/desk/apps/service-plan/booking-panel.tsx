import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import type { Customer, Schedule } from '@/types';
import { formatDay } from '../../ui/format';
import { bookedByText } from './booked-by';
import { PartLine } from './part-line';
import type { SelectedSlot } from './schedule-grid';
import { slotState } from './slot-state';

export function BookingPanel({
    schedule,
    customers,
    day,
    selected,
    onBook,
    onCancel,
}: {
    schedule: Schedule;
    customers: Customer[];
    day: string;
    selected: SelectedSlot | null;
    onBook: (customerId: number) => Promise<void>;
    onCancel: (appointmentId: number) => Promise<void>;
}) {
    const { t, locale } = useTranslation();
    const [customerId, setCustomerId] = useState('');
    const [isBusy, setBusy] = useState(false);

    if (!selected) {
        return (
            <div className="booking-panel">
                <p className="muted">{t('service_plan.pick_slot')}</p>
            </div>
        );
    }

    const technician = schedule.technicians.find(
        (entry) => entry.id === selected.technicianId,
    );
    const state = slotState(
        schedule,
        selected.technicianId,
        day,
        selected.slot,
    );
    const when = `${technician?.name}, ${formatDay(day, locale)} ${selected.slot}`;

    const run = async (action: () => Promise<void>) => {
        setBusy(true);
        await action().finally(() => setBusy(false));
    };

    if (state.kind === 'booked') {
        const { appointment } = state;

        return (
            <div className="booking-panel">
                <p>
                    <b>{appointment.customer.name}</b> &middot; {when}
                </p>
                <PartLine appointment={appointment} />
                {appointment.failed ? null : appointment.is_own ? (
                    <button
                        type="button"
                        className="button"
                        disabled={isBusy}
                        onClick={() => void run(() => onCancel(appointment.id))}
                    >
                        {t('service_plan.cancel')}
                    </button>
                ) : (
                    <p className="muted">{bookedByText(appointment, t)}</p>
                )}
            </div>
        );
    }

    return (
        <form
            className="booking-panel"
            onSubmit={(event) => {
                event.preventDefault();
                void run(() => onBook(Number(customerId)));
            }}
        >
            <p>
                <b>{t('service_plan.new_appointment')}</b> &middot; {when}
            </p>
            <label className="booking-customer" htmlFor="booking-customer">
                <span>{t('service_plan.customer')}</span>
                <select
                    id="booking-customer"
                    className="select"
                    value={customerId}
                    onChange={(event) => setCustomerId(event.target.value)}
                >
                    <option value="">
                        {t('service_plan.choose_customer')}
                    </option>
                    {customers.map((customer) => (
                        <option key={customer.id} value={customer.id}>
                            {customer.name} ({customer.city})
                        </option>
                    ))}
                </select>
            </label>
            <button
                type="submit"
                className="button button-primary"
                disabled={isBusy || customerId === ''}
            >
                {t('service_plan.book')}
            </button>
        </form>
    );
}
