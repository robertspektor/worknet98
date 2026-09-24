import type { CSSProperties } from 'react';

export function FloppyBoxArt({
    colors,
    label,
}: {
    colors: string[];
    label: string;
}) {
    return (
        <>
            <span className="floppy-box-tray" aria-hidden="true" />
            <span className="floppy-box-disks" aria-hidden="true">
                {colors.map((color, index) => (
                    <span
                        key={`${color}-${index}`}
                        className={`floppy-box-disk is-${color}`}
                        style={{ '--stack': index } as CSSProperties}
                    />
                ))}
            </span>
            <span className="floppy-box-lid" aria-hidden="true" />
            <span className="floppy-box-front">
                <span className="floppy-box-label">{label}</span>
            </span>
        </>
    );
}
