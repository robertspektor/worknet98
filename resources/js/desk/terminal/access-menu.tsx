import { useTranslation } from '@/i18n/use-translation';
import { TerminalPage } from './terminal-page';
import { TERMINAL_ID } from './terminal-id';
import type { TerminalRequest } from './terminal-state';
import { useHotkeys } from './use-hotkeys';

type Entry = {
    hotkey: string;
    request: TerminalRequest;
    heading: string;
    action: string;
};

function MenuEntry({
    entry,
    onChoose,
}: {
    entry: Entry;
    onChoose: () => void;
}) {
    return (
        <li>
            <button type="button" className="access-entry" onClick={onChoose}>
                <span className="entry-key">[{entry.hotkey}]</span>
                <span className="entry-text">
                    <span className="entry-heading">{entry.heading}</span>
                    <span className="entry-action">{entry.action}</span>
                </span>
            </button>
        </li>
    );
}

export function AccessMenu({
    city,
    onChoice,
}: {
    city: string | null;
    onChoice: (request: TerminalRequest) => void;
}) {
    const { t } = useTranslation();
    const place = (city ?? '').toUpperCase();

    const entries: Entry[] = [
        {
            hotkey: 'F1',
            request: 'new-resident',
            heading: t('terminal.menu_new_question', { city: place }),
            action: t('terminal.menu_new'),
        },
        {
            hotkey: 'F2',
            request: 'known-resident',
            heading: t('terminal.menu_known_question'),
            action: t('terminal.menu_known'),
        },
        {
            hotkey: 'F3',
            request: 'information',
            heading: t('terminal.menu_info_question'),
            action: t('terminal.menu_info', { city: city ?? '' }),
        },
    ];

    useHotkeys(
        Object.fromEntries(
            entries.flatMap((entry, index) =>
                [entry.hotkey, String(index + 1)].map((key) => [
                    key,
                    () => onChoice(entry.request),
                ]),
            ),
        ),
    );

    return (
        <TerminalPage city={city}>
            <p className="access-where">
                {t('terminal.access', { id: TERMINAL_ID })}
                <br />
                {t('terminal.office')}
            </p>
            <p className="access-welcome">{t('terminal.welcome')}</p>
            <ul className="access-entries">
                {entries.map((entry) => (
                    <MenuEntry
                        key={entry.hotkey}
                        entry={entry}
                        onChoose={() => onChoice(entry.request)}
                    />
                ))}
            </ul>
        </TerminalPage>
    );
}
