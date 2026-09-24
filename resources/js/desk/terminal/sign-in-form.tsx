import { useForm, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { store } from '@/routes/login-link';
import { AccessActions } from './access-actions';
import { AccessField } from './access-field';
import { TerminalPage } from './terminal-page';
import { useHotkeys } from './use-hotkeys';

export function SignInForm({
    city,
    onBack,
    onLinkSent,
}: {
    city: string | null;
    onBack: () => void;
    onLinkSent: () => void;
}) {
    const { t, locale } = useTranslation();
    const { status } = usePage().props;
    const form = useForm({ email: '', locale });
    useHotkeys({ Escape: onBack });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.transform((data) => ({ ...data, locale }));
        form.post(store.url(), {
            preserveState: true,
            onSuccess: onLinkSent,
        });
    };

    return (
        <TerminalPage city={city}>
            <h2 className="access-heading-line">
                {t('terminal.sign_in_title')}
            </h2>
            <form onSubmit={submit} noValidate>
                <p className="access-lead">{t('terminal.sign_in_lead')}</p>
                {status === 'login-link-invalid' && (
                    <p className="access-error">{t('terminal.invalid_link')}</p>
                )}
                <AccessField
                    id="access-email"
                    label={t('terminal.email')}
                    value={form.data.email}
                    error={form.errors.email}
                    onChange={(email) => form.setData('email', email)}
                />
                <AccessActions isBusy={form.processing} onBack={onBack} />
            </form>
        </TerminalPage>
    );
}
