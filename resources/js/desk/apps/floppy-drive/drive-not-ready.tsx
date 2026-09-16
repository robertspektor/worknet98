import { useTranslation } from '@/i18n/use-translation';
import { sound } from '../../sound/sound';
import { PixelIcon } from '../../ui/pixel-icon';

export function DriveNotReady() {
    const { t } = useTranslation();

    return (
        <div className="app-pad">
            <div className="notice">
                <PixelIcon name="warning" />
                <div>
                    <p className="drive-message">
                        {t('floppy_drive.not_ready')}
                    </p>
                    <p className="drive-message muted">
                        {t('floppy_drive.insert_hint')}
                    </p>
                </div>
            </div>
            <div className="app-actions app-actions-end">
                <button
                    type="button"
                    className="button"
                    onClick={() => sound.error()}
                >
                    {t('floppy_drive.retry')}
                </button>
            </div>
        </div>
    );
}
