import { router } from '@inertiajs/react';
import { update } from '@/routes/locale';

export function useLocaleSwitch() {
    return (locale: string) =>
        router.put(
            update.url(),
            { locale },
            { preserveState: true, preserveScroll: true },
        );
}
