export type TerminalScreen =
    | 'menu'
    | 'register'
    | 'sign-in'
    | 'info'
    | 'link-sent';

export type TerminalRequest =
    | 'new-resident'
    | 'known-resident'
    | 'information'
    | 'link-sent'
    | 'back';

const FROM_MENU: Record<string, TerminalScreen> = {
    'new-resident': 'register',
    'known-resident': 'sign-in',
    information: 'info',
};

export function nextTerminalScreen(
    screen: TerminalScreen,
    request: TerminalRequest,
): TerminalScreen {
    if (request === 'back') {
        return 'menu';
    }

    if (request === 'link-sent') {
        return screen === 'menu' ? screen : 'link-sent';
    }

    return screen === 'menu' ? (FROM_MENU[request] ?? screen) : screen;
}
