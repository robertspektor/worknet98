import { useState } from 'react';
import { PixelIcon } from '../../ui/pixel-icon';
import type { DiskFile } from './disk-files';

export function DiskFileList({
    files,
    onOpen,
}: {
    files: DiskFile[];
    onOpen: (file: DiskFile) => void;
}) {
    const [selected, setSelected] = useState<string | null>(null);

    return (
        <ul className="disk-file-list sunken">
            {files.map((file) => (
                <li key={file.name}>
                    <button
                        type="button"
                        className={`disk-file ${selected === file.name ? 'is-selected' : ''}`}
                        onPointerDown={() => setSelected(file.name)}
                        onDoubleClick={() => onOpen(file)}
                        onPointerUp={(event) =>
                            event.pointerType === 'touch' && onOpen(file)
                        }
                        onKeyDown={(event) =>
                            event.key === 'Enter' && onOpen(file)
                        }
                    >
                        <PixelIcon name={file.icon} />
                        <span className="disk-file-name">{file.name}</span>
                    </button>
                </li>
            ))}
        </ul>
    );
}
