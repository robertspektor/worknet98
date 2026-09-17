import { Head, router } from '@inertiajs/react';
import { useEffect, useRef } from 'react';
import { PlacementProvider } from '@/desk/placement/placement-provider';
import { Monitor } from '@/desk/room/monitor';
import { Room } from '@/desk/room/room';
import { StickyNote } from '@/desk/room/sticky-note';
import { SetupWindow } from '@/desk/setup/setup-window';
import { useTranslation } from '@/i18n/use-translation';
import { store } from '@/routes/login';

export default function RedeemLoginLink({ token }: { token: string }) {
    const { t } = useTranslation();
    const hasSubmitted = useRef(false);
    const redeem = () => router.post(store.url(token));

    useEffect(() => {
        if (hasSubmitted.current) {
            return;
        }

        hasSubmitted.current = true;
        router.post(store.url(token));
    }, [token]);

    return (
        <>
            <Head title={t('redeem.title')} />
            <PlacementProvider isSignedIn={false}>
                <Room scene="home">
                    <Monitor
                        isOn
                        onPowerPress={() => undefined}
                        accessory={<StickyNote />}
                    >
                        <SetupWindow title={t('setup.title')} icon="key">
                            <h2 className="app-heading">{t('redeem.title')}</h2>
                            <p>{t('redeem.body')}</p>
                            <div className="app-actions app-actions-end">
                                <button
                                    type="button"
                                    className="button button-primary"
                                    onClick={redeem}
                                >
                                    {t('redeem.button')}
                                </button>
                            </div>
                        </SetupWindow>
                    </Monitor>
                </Room>
            </PlacementProvider>
        </>
    );
}
