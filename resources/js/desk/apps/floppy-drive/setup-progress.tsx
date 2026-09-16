import type { CSSProperties } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { SETUP_DURATION_MS } from './use-program-setup';

export function SetupProgress() {
    const { t } = useTranslation();

    return (
        <div className="app-pad setup-progress">
            <p>{t('program_setup.copying')}</p>
            <div
                className="progress-bar sunken"
                style={
                    { '--duration': `${SETUP_DURATION_MS}ms` } as CSSProperties
                }
            >
                <span className="progress-fill" />
            </div>
        </div>
    );
}
