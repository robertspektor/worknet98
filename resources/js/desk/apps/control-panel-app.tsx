import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { LocaleSelect } from '../ui/locale-select';
import { PixelIcon } from '../ui/pixel-icon';
import { useLocaleSwitch } from '../ui/use-locale-switch';

export function ControlPanelApp() {
    const { t, locale } = useTranslation();
    const switchLocale = useLocaleSwitch();
    const [selected, setSelected] = useState(locale);

    return (
        <div className="app-pad">
            <div className="app-header">
                <PixelIcon name="control-panel" />
                <h2 className="app-heading">{t('control_panel.title')}</h2>
            </div>
            <label className="field" htmlFor="control-panel-locale">
                <span>{t('control_panel.language')}</span>
                <LocaleSelect
                    id="control-panel-locale"
                    value={selected}
                    onChange={setSelected}
                />
            </label>
            <p className="muted">{t('control_panel.language_hint')}</p>
            <div className="app-actions app-actions-end">
                <button
                    type="button"
                    className="button button-primary"
                    disabled={selected === locale}
                    onClick={() => switchLocale(selected)}
                >
                    {t('control_panel.apply')}
                </button>
            </div>
        </div>
    );
}
