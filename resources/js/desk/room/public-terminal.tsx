import { useTranslation } from '@/i18n/use-translation';
import { useComputerMachine } from '../computer/computer-provider';
import { sound } from '../sound/sound';
import { TERMINAL_ID } from '../terminal/terminal-id';
import { PowerButton } from './power-button';

/* Same manufacturer as the machine at home, built for a hall: the drive bay
   is blanked off, the case is locked and the city has stuck its own label on
   it. Only the switch is left for whoever walks up. */

export function PublicTerminal({
    city,
    powerLabel,
}: {
    city: string | null;
    powerLabel?: string;
}) {
    const { t } = useTranslation();
    const computer = useComputerMachine();

    const pressPower = () => {
        sound.unlock();
        computer.togglePower();
    };

    return (
        <div className="desk-item public-terminal">
            <div className="terminal-case" data-surface="ledge">
                <span className="terminal-side" aria-hidden="true" />
                <span className="terminal-blank" aria-hidden="true" />
                <span className="terminal-vents" aria-hidden="true">
                    <span />
                    <span />
                    <span />
                    <span />
                </span>
                {city !== null && (
                    <span className="terminal-sticker">
                        {t('terminal.sticker', {
                            city: city.toUpperCase(),
                            id: TERMINAL_ID,
                        })}
                    </span>
                )}
                <div className="terminal-front">
                    <PowerButton
                        isOn={computer.isOn}
                        isHinting={computer.state === 'off'}
                        label={powerLabel}
                        onPress={pressPower}
                    />
                    <span className="terminal-lock" aria-hidden="true" />
                </div>
                <div className="terminal-foot" aria-hidden="true">
                    <span className="brand">RETROTRON</span>
                    <span className="terminal-plate">INV 04-1187</span>
                </div>
            </div>
        </div>
    );
}
