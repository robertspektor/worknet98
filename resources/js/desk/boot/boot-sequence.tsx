import { useState } from 'react';
import { sound } from '../sound/sound';
import { BiosScreen } from './bios-screen';
import { SplashScreen } from './splash-screen';

export function BootSequence({ onDone }: { onDone: () => void }) {
    const [phase, setPhase] = useState<'bios' | 'splash'>('bios');

    if (phase === 'bios') {
        return <BiosScreen onDone={() => setPhase('splash')} />;
    }

    return (
        <SplashScreen
            onDone={() => {
                sound.startup();
                onDone();
            }}
        />
    );
}
