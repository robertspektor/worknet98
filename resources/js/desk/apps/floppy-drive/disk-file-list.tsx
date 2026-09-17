import { useTranslation } from '@/i18n/use-translation';
import type { DiskFile } from '@/types';
import { PixelIcon } from '../../ui/pixel-icon';
import { fileIcon } from './disk-files';

export function DiskFileList({
    files,
    selectedId,
    onSelect,
    onOpen,
}: {
    files: DiskFile[];
    selectedId: number | null;
    onSelect: (file: DiskFile) => void;
    onOpen: (file: DiskFile) => void;
}) {
    const { t } = useTranslation();

    if (files.length === 0) {
        return (
            <p className="disk-file-list is-empty sunken muted">
                {t('floppy_drive.empty')}
            </p>
        );
    }

    return (
        <ul className="disk-file-list sunken">
            {files.map((file) => (
                <li key={file.id}>
                    <button
                        type="button"
                        className={`disk-file ${selectedId === file.id ? 'is-selected' : ''}`}
                        onPointerDown={() => onSelect(file)}
                        onFocus={() => onSelect(file)}
                        onDoubleClick={() => onOpen(file)}
                        onPointerUp={(event) =>
                            event.pointerType === 'touch' && onOpen(file)
                        }
                        onKeyDown={(event) =>
                            event.key === 'Enter' && onOpen(file)
                        }
                    >
                        <PixelIcon name={fileIcon(file)} />
                        <span className="disk-file-name">{file.name}</span>
                    </button>
                </li>
            ))}
        </ul>
    );
}
