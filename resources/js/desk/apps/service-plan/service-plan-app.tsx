import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import {
    destroy as appointmentsDestroy,
    store as appointmentsStore,
} from '@/routes/api/v1/appointments';
import { index as customersIndex } from '@/routes/api/v1/customers';
import { show as scheduleShow } from '@/routes/api/v1/schedule';
import type { Customer, Schedule } from '@/types';
import { deleteJson, postJson } from '../../api/game-api';
import { useApiResource } from '../../api/use-api-resource';
import { useCompanySoftware } from '../../company-software/company-software-provider';
import { sound } from '../../sound/sound';
import { AppLoading } from '../../ui/app-loading';
import { formatDay } from '../../ui/format';
import { useRefusalAlert } from '../../ui/use-refusal-alert';
import { BookingPanel } from './booking-panel';
import { ScheduleGrid } from './schedule-grid';
import type { SelectedSlot } from './schedule-grid';

export function ServicePlanApp() {
    const { t, locale } = useTranslation();
    const software = useCompanySoftware();
    const alertRefusal = useRefusalAlert();
    const schedule = useApiResource<Schedule>(scheduleShow.url());
    const customers = useApiResource<Customer[]>(customersIndex.url());
    const [dayIndex, setDayIndex] = useState(0);
    const [selected, setSelected] = useState<SelectedSlot | null>(null);

    if (!schedule.data || !customers.data) {
        return <AppLoading />;
    }

    const day = schedule.data.days[dayIndex];
    const title = software?.app_names.scheduler ?? '';

    const book = async (customerId: number) => {
        if (!selected) {
            return;
        }

        try {
            await postJson(appointmentsStore.url(), {
                customer_id: customerId,
                technician_id: selected.technicianId,
                date: day,
                slot: selected.slot,
            });
            sound.click();
            schedule.reload();
        } catch (error) {
            alertRefusal(title, error);
        }
    };

    const cancel = async (appointmentId: number) => {
        try {
            await deleteJson(appointmentsDestroy.url(appointmentId));
            schedule.reload();
        } catch (error) {
            alertRefusal(title, error);
        }
    };

    return (
        <div className="company-app">
            <header className="company-app-bar">
                <span className="company-app-name">{title}</span>
                <span className="company-app-company">{software?.company}</span>
            </header>
            <div className="day-tabs" role="tablist">
                {schedule.data.days.map((entry, index) => (
                    <button
                        key={entry}
                        type="button"
                        role="tab"
                        aria-selected={index === dayIndex}
                        className={`day-tab ${index === dayIndex ? 'is-active' : ''}`}
                        onClick={() => {
                            setDayIndex(index);
                            setSelected(null);
                        }}
                    >
                        {formatDay(entry, locale)}
                    </button>
                ))}
            </div>
            <div className="schedule-body sunken">
                <ScheduleGrid
                    schedule={schedule.data}
                    day={day}
                    selected={selected}
                    onSelect={setSelected}
                />
            </div>
            <BookingPanel
                key={`${day}-${selected?.technicianId}-${selected?.slot}`}
                schedule={schedule.data}
                customers={customers.data}
                day={day}
                selected={selected}
                onBook={book}
                onCancel={cancel}
            />
            <p className="muted schedule-hint">{t('service_plan.hint')}</p>
        </div>
    );
}
