import { useTranslation } from '@/i18n/use-translation';
import { usePlaceable } from '../placement/use-placeable';
import { FloppyBoxArt } from './floppy-box-art';

const STARTER_COLORS = ['blue', 'green', 'red'];

export function DeskDiskBox() {
    const { t } = useTranslation();
    const { className, ...placeable } =
        usePlaceable<HTMLDivElement>('floppy-box');

    return (
        <div
            className={`desk-item floppy-box ${className}`}
            {...placeable}
            aria-hidden="true"
        >
            <span className="floppy-box-body">
                <FloppyBoxArt
                    colors={STARTER_COLORS}
                    label={t('floppy_box.label')}
                />
            </span>
        </div>
    );
}
