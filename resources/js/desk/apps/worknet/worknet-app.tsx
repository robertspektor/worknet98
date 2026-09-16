import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import type { JobOpening } from '@/types';
import { GameApiError } from '../../api/game-api';
import { useDialogs } from '../../dialogs/dialog-provider';
import { useMailbox } from '../../mailbox/mailbox-provider';
import { sound } from '../../sound/sound';
import { AppLoading } from '../../ui/app-loading';
import { applicationFor } from './job-board-state';
import { JobDetail } from './job-detail';
import { JobList } from './job-list';
import { useJobBoard } from './use-job-board';

function WorkNetBanner() {
    const { t } = useTranslation();

    return (
        <header className="worknet-banner">
            <span className="worknet-logo">
                Work<b>Net</b> 98
            </span>
            <span className="worknet-slogan">{t('worknet.slogan')}</span>
        </header>
    );
}

function ConnectionError({ onRetry }: { onRetry: () => void }) {
    const { t } = useTranslation();

    return (
        <div className="worknet-error">
            <p>{t('worknet.error')}</p>
            <button type="button" className="button" onClick={onRetry}>
                {t('worknet.retry')}
            </button>
        </div>
    );
}

export function WorkNetApp() {
    const { t } = useTranslation();
    const dialogs = useDialogs();
    const mailbox = useMailbox();
    const jobBoard = useJobBoard(mailbox.emails?.length ?? 0);
    const [selectedId, setSelectedId] = useState<number | null>(null);
    const board = jobBoard.board;
    const selected =
        board?.openings.find((opening) => opening.id === selectedId) ?? null;

    const apply = async (opening: JobOpening, message: string) => {
        try {
            await jobBoard.apply(opening, message);
            void dialogs.alert({
                title: t('worknet.sent_title'),
                message: t('worknet.sent_message', {
                    company: opening.company.name,
                }),
                icon: 'mail',
            });
        } catch (error) {
            sound.error();
            void dialogs.alert({
                title: t('worknet.refused_title'),
                message:
                    error instanceof GameApiError
                        ? error.message
                        : t('worknet.error'),
                icon: 'warning',
            });
        }
    };

    const content = () => {
        if (jobBoard.hasFailed) {
            return <ConnectionError onRetry={jobBoard.reload} />;
        }

        if (!board) {
            return <AppLoading labelKey="worknet.loading" />;
        }

        return selected ? (
            <JobDetail
                opening={selected}
                application={applicationFor(board.applications, selected.id)}
                onBack={() => setSelectedId(null)}
                onApply={(message) => apply(selected, message)}
            />
        ) : (
            <JobList
                openings={board.openings}
                applications={board.applications}
                onSelect={(opening) => setSelectedId(opening.id)}
            />
        );
    };

    return (
        <div className="worknet">
            <WorkNetBanner />
            <div className="worknet-page">{content()}</div>
        </div>
    );
}
