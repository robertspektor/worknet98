import { useTranslation } from '@/i18n/use-translation';
import { GameApiError } from '../api/game-api';
import { useDialogs } from '../dialogs/dialog-provider';
import { sound } from '../sound/sound';

export function useRefusalAlert(): (title: string, error: unknown) => void {
    const { t } = useTranslation();
    const dialogs = useDialogs();

    return (title, error) => {
        sound.error();
        void dialogs.alert({
            title,
            message:
                error instanceof GameApiError
                    ? error.message
                    : t('app.connection_error'),
            icon: 'warning',
        });
    };
}
