import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import type { ShiftStatus } from '@/types';
import { GameApiError } from '../../api/game-api';
import { useDialogs } from '../../dialogs/dialog-provider';
import { useShiftClock } from '../../shift/shift-clock-provider';
import { sound } from '../../sound/sound';
import { AppLoading } from '../../ui/app-loading';
import { PixelIcon } from '../../ui/pixel-icon';
import { NextPeriodCountdown } from './next-period-countdown';
import { StatusLine } from './status-line';
import { WorkTimeSheet } from './work-time-sheet';

export function TimeClockApp() {
    const { t } = useTranslation();
    const dialogs = useDialogs();
    const { shift, clockIn, clockOut } = useShiftClock();
    const [isBusy, setBusy] = useState(false);

    if (!shift) {
        return <AppLoading />;
    }

    const run = async (action: () => Promise<ShiftStatus>) => {
        setBusy(true);

        try {
            await action();
            sound.click();
        } catch (error) {
            sound.error();
            void dialogs.alert({
                title: t('time_clock.refused_title'),
                message: error instanceof GameApiError ? error.message : '',
                icon: 'warning',
            });
        } finally {
            setBusy(false);
        }
    };

    const isOnDuty = shift.status === 'on_duty';

    return (
        <div className="app-pad">
            <div className="app-header">
                <PixelIcon name="clock" />
                <h2 className="app-heading">{t('time_clock.title')}</h2>
            </div>
            <div className="time-clock-panel sunken">
                <StatusLine shift={shift} />
                <WorkTimeSheet shift={shift} />
                <NextPeriodCountdown endsAt={shift.period.ends_at} />
            </div>
            <div className="app-actions app-actions-end">
                <button
                    type="button"
                    className="button button-primary"
                    disabled={isBusy}
                    onClick={() => void run(isOnDuty ? clockOut : clockIn)}
                >
                    {t(
                        isOnDuty
                            ? 'time_clock.clock_out'
                            : 'time_clock.clock_in',
                    )}
                </button>
            </div>
        </div>
    );
}
