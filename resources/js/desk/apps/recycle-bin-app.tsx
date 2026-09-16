import { useTranslation } from '@/i18n/use-translation';
import { useDialogs } from '../dialogs/dialog-provider';
import { sound } from '../sound/sound';
import { PixelIcon } from '../ui/pixel-icon';

const FILE_KEYS = [
    'recycle_bin.file_1',
    'recycle_bin.file_2',
    'recycle_bin.file_3',
    'recycle_bin.file_4',
];

export function RecycleBinApp() {
    const { t } = useTranslation();
    const dialogs = useDialogs();

    const empty = () => {
        sound.error();
        void dialogs.alert({
            title: t('recycle_bin.denied_title'),
            message: t('recycle_bin.denied_message'),
            icon: 'warning',
        });
    };

    return (
        <div className="app-pad">
            <ul className="file-list sunken">
                {FILE_KEYS.map((key) => (
                    <li key={key}>
                        <PixelIcon name="program" size={16} />
                        <span>{t(key)}</span>
                    </li>
                ))}
            </ul>
            <div className="app-actions app-actions-end">
                <button type="button" className="button" onClick={empty}>
                    {t('recycle_bin.empty')}
                </button>
            </div>
        </div>
    );
}
