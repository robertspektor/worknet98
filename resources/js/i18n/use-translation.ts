import { usePage } from '@inertiajs/react';
import type { Replacements } from './translate';
import { translate } from './translate';

export function useTranslation() {
    const { translations, locale } = usePage().props;

    return {
        locale,
        t: (key: string, replacements?: Replacements) =>
            translate(translations, key, replacements),
    };
}
