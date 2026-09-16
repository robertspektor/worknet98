import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import type { ShiftStatus } from '@/types';
import { GameApiError } from '../../api/game-api';
import { useDialogs } from '../../dialogs/dialog-provider';
import { sound } from '../../sound/sound';
import { AppLoading } from '../../ui/app-loading';
import { formatAmount } from '../../ui/format';
import { PixelIcon } from '../../ui/pixel-icon';
import { useShift } from './use-shift';

function StatusLine({ shift }: { shift: ShiftStatus }) {
    const { t, locale } = useTranslation();
    const time = shift.clocked_in_at
        ? new Date(shift.clocked_in_at).toLocaleTimeString(locale, {
              hour: '2-digit',
              minute: '2-digit',
          })
        : '';

    return (
        <p className={`time-clock-status is-${shift.status}`}>
            {t(`time_clock.status.${shift.status}`, { time })}
        </p>
    );
}

export function TimeClockApp() {
    const { t, locale } = useTranslation();
    const dialogs = useDialogs();
    const clock = useShift();
    const [isBusy, setBusy] = useState(false);
    const shift = clock.shift;

    if (!shift) {
        return <AppLoading />;
    }

    const run = async (action: () => Promise<ShiftStatus>) => {
        setBusy(true);

        try {
            const next = await action();
            sound.click();

            if (next.status === 'done') {
                void dialogs.alert({
                    title: t('time_clock.paid_title'),
                    message: t('time_clock.paid_message', {
                        amount: formatAmount(next.daily_salary, locale),
                    }),
                    icon: 'clock',
                });
            }
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

    return (
        <div className="app-pad">
            <div className="app-header">
                <PixelIcon name="clock" />
                <h2 className="app-heading">{t('time_clock.title')}</h2>
            </div>
            <div className="time-clock-panel sunken">
                <StatusLine shift={shift} />
                <p className="muted">
                    {t('time_clock.salary')}:{' '}
                    {t('money.amount', {
                        amount: formatAmount(shift.daily_salary, locale),
                    })}
                </p>
            </div>
            <div className="app-actions app-actions-end">
                {shift.status === 'off_duty' && (
                    <button
                        type="button"
                        className="button button-primary"
                        disabled={isBusy}
                        onClick={() => void run(clock.clockIn)}
                    >
                        {t('time_clock.clock_in')}
                    </button>
                )}
                {shift.status === 'on_duty' && (
                    <button
                        type="button"
                        className="button button-primary"
                        disabled={isBusy}
                        onClick={() => void run(clock.clockOut)}
                    >
                        {t('time_clock.clock_out')}
                    </button>
                )}
            </div>
        </div>
    );
}
