import { useTranslation } from '@/i18n/use-translation';
import { usePlayerProfile } from '../api/use-player-profile';
import { PixelIcon } from '../ui/pixel-icon';

export function MyComputerApp() {
    const { t } = useTranslation();
    const player = usePlayerProfile();

    return (
        <div className="app-pad">
            <div className="app-header">
                <PixelIcon name="computer" />
                <h2 className="app-heading">{t('my_computer.title')}</h2>
            </div>
            <dl className="property-list sunken">
                <dt>{t('my_computer.system')}</dt>
                <dd>{t('my_computer.os')}</dd>
                <dt>{t('my_computer.registered_to')}</dt>
                <dd>{player?.email ?? '...'}</dd>
                <dt>{t('my_computer.computer')}</dt>
                <dd>{t('my_computer.hardware')}</dd>
                <dt>{t('my_computer.status')}</dt>
                <dd>
                    {player?.employer
                        ? t('my_computer.status_employed', {
                              company: player.employer,
                          })
                        : t('my_computer.status_value')}
                </dd>
            </dl>
        </div>
    );
}
