import { useTranslation } from '@/i18n/use-translation';

export function AssetTag({ company }: { company: string }) {
    const { t } = useTranslation();

    return (
        <div className="asset-tag">
            <span className="asset-tag-text">
                {t('office.asset_tag', { company })}
            </span>
            <span className="asset-tag-barcode" aria-hidden="true" />
        </div>
    );
}
