import { useState } from 'react';
import { NetworkScreen } from './network-screen';
import { SelfTest } from './self-test';

export function TerminalBoot({
    city,
    onDone,
}: {
    city: string | null;
    onDone: () => void;
}) {
    const [phase, setPhase] = useState<'self-test' | 'network'>('self-test');

    if (phase === 'self-test') {
        return <SelfTest onDone={() => setPhase('network')} />;
    }

    return <NetworkScreen city={city} onDone={onDone} />;
}
