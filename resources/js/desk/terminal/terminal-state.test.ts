import { describe, expect, it } from 'vite-plus/test';
import { nextTerminalScreen } from './terminal-state';

describe('public access terminal', () => {
    it('opens the screen the visitor picked in the menu', () => {
        expect(nextTerminalScreen('menu', 'new-resident')).toBe('register');
        expect(nextTerminalScreen('menu', 'known-resident')).toBe('sign-in');
        expect(nextTerminalScreen('menu', 'information')).toBe('info');
    });

    it('takes every screen back to the menu', () => {
        expect(nextTerminalScreen('register', 'back')).toBe('menu');
        expect(nextTerminalScreen('sign-in', 'back')).toBe('menu');
        expect(nextTerminalScreen('info', 'back')).toBe('menu');
        expect(nextTerminalScreen('link-sent', 'back')).toBe('menu');
    });

    it('reports a sent sign-in link from both forms', () => {
        expect(nextTerminalScreen('register', 'link-sent')).toBe('link-sent');
        expect(nextTerminalScreen('sign-in', 'link-sent')).toBe('link-sent');
    });

    /* The menu has no address to send anything to, and a screen only ever
       leaves through its own keys. */

    it('ignores what cannot happen on the screen in front of the visitor', () => {
        expect(nextTerminalScreen('menu', 'link-sent')).toBe('menu');
        expect(nextTerminalScreen('register', 'known-resident')).toBe(
            'register',
        );
        expect(nextTerminalScreen('info', 'new-resident')).toBe('info');
    });
});
