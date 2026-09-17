import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { index as shipmentsIndex } from '@/routes/api/v1/shipments';
import {
    destroy as planDestroy,
    update as planUpdate,
} from '@/routes/api/v1/shipments/plan';
import { show as tourPlanShow } from '@/routes/api/v1/tour-plan';
import type { Shipment, TourPlan } from '@/types';
import { deleteJson, putJson } from '../../api/game-api';
import { useApiResource } from '../../api/use-api-resource';
import { useCompanySoftware } from '../../company-software/company-software-provider';
import { sound } from '../../sound/sound';
import { AppLoading } from '../../ui/app-loading';
import { formatDay } from '../../ui/format';
import { useRefusalAlert } from '../../ui/use-refusal-alert';
import { TourGrid } from './tour-grid';
import type { SelectedTour } from './tour-grid';
import { TourPanel } from './tour-panel';

export function TourPlannerApp() {
    const { t, locale } = useTranslation();
    const software = useCompanySoftware();
    const alertRefusal = useRefusalAlert();
    const plan = useApiResource<TourPlan>(tourPlanShow.url());
    const shipments = useApiResource<Shipment[]>(shipmentsIndex.url());
    const [dayIndex, setDayIndex] = useState(0);
    const [selected, setSelected] = useState<SelectedTour | null>(null);

    if (!plan.data || !shipments.data) {
        return <AppLoading />;
    }

    const day = plan.data.days[dayIndex];
    const title = software?.app_names.tours ?? '';

    const change = async (request: () => Promise<unknown>) => {
        try {
            await request();
            sound.click();
            shipments.reload();
        } catch (error) {
            alertRefusal(title, error);
        }
    };

    const planShipment = (shipmentId: number) =>
        change(() =>
            putJson(planUpdate.url(shipmentId), {
                driver_id: selected?.driverId,
                date: day,
                tour: selected?.tour,
            }),
        );

    const unplanShipment = (shipmentId: number) =>
        change(() => deleteJson(planDestroy.url(shipmentId)));

    return (
        <div className="company-app">
            <header className="company-app-bar">
                <span className="company-app-name">{title}</span>
                <span className="company-app-company">{software?.company}</span>
            </header>
            <div className="day-tabs" role="tablist">
                {plan.data.days.map((entry, index) => (
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
                <TourGrid
                    plan={plan.data}
                    shipments={shipments.data}
                    day={day}
                    selected={selected}
                    onSelect={setSelected}
                />
            </div>
            <TourPanel
                key={`${day}-${selected?.driverId}-${selected?.tour}`}
                plan={plan.data}
                shipments={shipments.data}
                day={day}
                selected={selected}
                onPlan={planShipment}
                onUnplan={unplanShipment}
            />
            <p className="muted schedule-hint">{t('tour_planner.hint')}</p>
        </div>
    );
}
