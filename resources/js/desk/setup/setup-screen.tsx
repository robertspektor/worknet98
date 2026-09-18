import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { SetupWindow } from './setup-window';
import { SignInForm } from './sign-in-form';

export function SetupScreen() {
    const { t } = useTranslation();
    const [isLinkSent, setLinkSent] = useState(false);

    if (!isLinkSent) {
        return (
            <SetupWindow title={t('setup.title')} icon="key">
                <SignInForm onSent={() => setLinkSent(true)} />
            </SetupWindow>
        );
    }

    return (
        <SetupWindow title={t('setup.title')} icon="info">
            <h2 className="app-heading">{t('setup.sent_title')}</h2>
            <p>{t('setup.sent_body')}</p>
            <div className="app-actions app-actions-end">
                <button
                    type="button"
                    className="button"
                    onClick={() => setLinkSent(false)}
                >
                    {t('setup.try_again')}
                </button>
            </div>
        </SetupWindow>
    );
}
