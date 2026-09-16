import { useTranslation } from '@/i18n/use-translation';
import type { FloppyDisk } from '@/types';

export function ReadmeViewer({
    disk,
    onClose,
}: {
    disk: FloppyDisk;
    onClose: () => void;
}) {
    const { t } = useTranslation();

    return (
        <div className="drive-readme">
            <pre className="readme-text sunken">
                {t(`floppy_disk.${disk.slug}.readme`)}
            </pre>
            <div className="app-actions app-actions-end">
                <button type="button" className="button" onClick={onClose}>
                    {t('floppy_drive.back')}
                </button>
            </div>
        </div>
    );
}
