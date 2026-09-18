import { useForm, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { store } from '@/routes/sign-in';
import { LocaleSelect } from '../ui/locale-select';
import { useLocaleSwitch } from '../ui/use-locale-switch';

export function SignInForm({ onSent }: { onSent: () => void }) {
    const { t, locale } = useTranslation();
    const { status } = usePage().props;
    const switchLocale = useLocaleSwitch();
    const form = useForm({ email: '', age_confirmed: false, locale });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.transform((data) => ({ ...data, locale }));
        form.post(store.url(), {
            preserveState: true,
            onSuccess: (page) => {
                if (page.props.status === 'login-link-sent') {
                    onSent();
                }
            },
        });
    };

    return (
        <form onSubmit={submit} noValidate>
            <p>{t('setup.welcome')}</p>
            {status === 'login-link-invalid' && (
                <p className="field-error">{t('setup.invalid_link')}</p>
            )}
            <label className="field" htmlFor="setup-locale">
                <span>{t('setup.language')}</span>
                <LocaleSelect
                    id="setup-locale"
                    value={locale}
                    onChange={switchLocale}
                />
            </label>
            <label className="field" htmlFor="setup-email">
                <span>{t('setup.email')}</span>
                <input
                    id="setup-email"
                    className="input"
                    type="email"
                    autoComplete="email"
                    autoFocus
                    value={form.data.email}
                    onChange={(event) =>
                        form.setData('email', event.target.value)
                    }
                />
                {form.errors.email && (
                    <span className="field-error">{form.errors.email}</span>
                )}
            </label>
            <label className="checkbox-field">
                <input
                    type="checkbox"
                    checked={form.data.age_confirmed}
                    onChange={(event) =>
                        form.setData('age_confirmed', event.target.checked)
                    }
                />
                <span>{t('setup.age_confirmed')}</span>
            </label>
            {form.errors.age_confirmed && (
                <span className="field-error">{form.errors.age_confirmed}</span>
            )}
            <div className="app-actions app-actions-end">
                <button
                    type="submit"
                    className="button button-primary"
                    disabled={form.processing}
                >
                    {t('setup.submit')}
                </button>
            </div>
        </form>
    );
}
