import { useReducer } from 'react';
import type { CityFigures } from '@/types';
import { AccessMenu } from './access-menu';
import { InfoScreen } from './info-screen';
import { LinkSent } from './link-sent';
import { RegisterForm } from './register-form';
import { SignInForm } from './sign-in-form';
import type { TerminalScreen } from './terminal-state';
import { nextTerminalScreen } from './terminal-state';

export function AccessScreen({
    city,
    figures,
    initialScreen = 'menu',
}: {
    city: string | null;
    figures: CityFigures | null;
    initialScreen?: TerminalScreen;
}) {
    const [screen, request] = useReducer(nextTerminalScreen, initialScreen);
    const back = () => request('back');
    const linkSent = () => request('link-sent');

    if (screen === 'register') {
        return <RegisterForm city={city} onBack={back} onLinkSent={linkSent} />;
    }

    if (screen === 'sign-in') {
        return <SignInForm city={city} onBack={back} onLinkSent={linkSent} />;
    }

    if (screen === 'info') {
        return <InfoScreen city={city} figures={figures} onBack={back} />;
    }

    if (screen === 'link-sent') {
        return <LinkSent city={city} onBack={back} />;
    }

    return <AccessMenu city={city} onChoice={request} />;
}
