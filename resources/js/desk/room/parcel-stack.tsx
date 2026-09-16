import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { useFloppyDrive } from '../floppy/floppy-drive-provider';
import { sound } from '../sound/sound';

const UNPACK_MS = 600;

export function ParcelStack() {
    const { t } = useTranslation();
    const { parcels, unpack } = useFloppyDrive();
    const [isOpening, setOpening] = useState(false);
    const next = parcels.at(0);

    if (!next) {
        return null;
    }

    const open = () => {
        if (isOpening) {
            return;
        }

        sound.floppyEject();
        setOpening(true);
        setTimeout(() => {
            void unpack(next).finally(() => setOpening(false));
        }, UNPACK_MS);
    };

    return (
        <button
            type="button"
            className={`desk-item parcel-stack ${isOpening ? 'is-opening' : ''}`}
            aria-label={t('parcel.unpack')}
            title={t('parcel.unpack')}
            onClick={open}
        >
            {parcels.length > 1 && (
                <span className="parcel parcel-behind" aria-hidden="true" />
            )}
            <span className="parcel" aria-hidden="true">
                <span className="parcel-flap is-left" />
                <span className="parcel-flap is-right" />
                <span className="parcel-tape" />
                <span className="parcel-label">DiskDepot</span>
            </span>
            <span className="desk-item-caption">{t('parcel.caption')}</span>
        </button>
    );
}
