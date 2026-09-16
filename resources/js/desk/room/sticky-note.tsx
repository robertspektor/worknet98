import { useTranslation } from '@/i18n/use-translation';

export function StickyNote() {
    const { t } = useTranslation();

    return (
        <div className="sticky-note">
            {t('sticky_note.line_1')}
            <br />
            {t('sticky_note.line_2')}
            <br />
            {t('sticky_note.line_3')}
        </div>
    );
}
