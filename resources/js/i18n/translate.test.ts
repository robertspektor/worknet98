import { describe, expect, it } from 'vite-plus/test';
import { translate } from './translate';

describe('translate', () => {
    const translations = { 'mail.expiry': 'Expires in :minutes minutes.' };

    it('replaces named placeholders', () => {
        expect(translate(translations, 'mail.expiry', { minutes: 15 })).toBe(
            'Expires in 15 minutes.',
        );
    });

    it('falls back to the key for missing translations', () => {
        expect(translate(translations, 'missing.key')).toBe('missing.key');
    });
});
