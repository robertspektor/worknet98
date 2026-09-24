import { useTranslation } from '@/i18n/use-translation';

/* The way back out of a detail view: a small frame in the corner of the
   window that stands for the whole room. */

export function OverviewButton({ onPress }: { onPress: () => void }) {
    const { t } = useTranslation();

    return (
        <button
            type="button"
            className="overview-button"
            title={t('camera.overview')}
            aria-label={t('camera.overview')}
            onClick={onPress}
        >
            <span className="overview-icon" aria-hidden="true">
                <span className="overview-corner is-top-left" />
                <span className="overview-corner is-top-right" />
                <span className="overview-corner is-bottom-left" />
                <span className="overview-corner is-bottom-right" />
            </span>
        </button>
    );
}
