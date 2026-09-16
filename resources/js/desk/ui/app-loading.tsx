import { useTranslation } from '@/i18n/use-translation';

export function AppLoading({
    labelKey = 'app.loading',
}: {
    labelKey?: string;
}) {
    const { t } = useTranslation();

    return <p className="app-loading">{t(labelKey)}</p>;
}
