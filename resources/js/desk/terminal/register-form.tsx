import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { store } from '@/routes/sign-in';
import { AccessActions } from './access-actions';
import { AccessField } from './access-field';
import { TerminalPage } from './terminal-page';
import { useHotkeys } from './use-hotkeys';

export function RegisterForm({
    city,
    onBack,
    onLinkSent,
}: {
    city: string | null;
    onBack: () => void;
    onLinkSent: () => void;
}) {
    const { t, locale } = useTranslation();
    const form = useForm({ email: '', age_confirmed: false, locale });
    useHotkeys({ Escape: onBack });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.transform((data) => ({ ...data, locale }));

        /* A registration leaves the terminal for good, so only an address
           that is taken already comes back to this screen. */

        form.post(store.url(), {
            preserveState: true,
            onSuccess: (page) => {
                if (page.props.status === 'login-link-sent') {
                    onLinkSent();
                }
            },
        });
    };

    return (
        <TerminalPage city={city}>
            <h2 className="access-heading-line">
                {t('terminal.register_title')}
            </h2>
            <form onSubmit={submit} noValidate>
                <p className="access-lead">{t('terminal.register_lead')}</p>
                <AccessField
                    id="access-email"
                    label={t('terminal.email')}
                    value={form.data.email}
                    error={form.errors.email}
                    onChange={(email) => form.setData('email', email)}
                />
                <label className="access-check">
                    <input
                        type="checkbox"
                        checked={form.data.age_confirmed}
                        onChange={(event) =>
                            form.setData('age_confirmed', event.target.checked)
                        }
                    />
                    <span>{t('terminal.age_confirmed')}</span>
                </label>
                {form.errors.age_confirmed && (
                    <p className="access-error">{form.errors.age_confirmed}</p>
                )}
                <AccessActions isBusy={form.processing} onBack={onBack} />
            </form>
        </TerminalPage>
    );
}
