import { useTranslation } from '@/i18n/use-translation';
import { usePlaceable } from '../placement/use-placeable';

export function StickyNote() {
    const { t } = useTranslation();
    const { className, ...placeable } =
        usePlaceable<HTMLDivElement>('sticky-note');

    return (
        <div className={`sticky-note ${className}`} {...placeable}>
            {t('sticky_note.line_1')}
            <br />
            {t('sticky_note.line_2')}
            <br />
            {t('sticky_note.line_3')}
        </div>
    );
}
