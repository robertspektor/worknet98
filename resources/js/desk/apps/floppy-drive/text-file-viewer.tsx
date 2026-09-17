import { useTranslation } from '@/i18n/use-translation';
import type { DiskFile } from '@/types';

export function TextFileViewer({
    file,
    onClose,
}: {
    file: DiskFile;
    onClose: () => void;
}) {
    const { t } = useTranslation();

    return (
        <div className="drive-text-file">
            <pre className="readme-text sunken">{file.text}</pre>
            <div className="app-actions app-actions-end">
                <button type="button" className="button" onClick={onClose}>
                    {t('floppy_drive.back')}
                </button>
            </div>
        </div>
    );
}
