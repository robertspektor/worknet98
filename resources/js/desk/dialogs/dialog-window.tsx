import { useTranslation } from '@/i18n/use-translation';
import { PixelGlyph, PixelIcon } from '../ui/pixel-icon';
import type { DialogRequest } from './dialog-provider';

export function DialogWindow({
    request,
    onAnswer,
}: {
    request: DialogRequest;
    onAnswer: (confirmed: boolean) => void;
}) {
    const { t } = useTranslation();

    return (
        <div className="dialog-overlay">
            <section
                className="window dialog is-focused"
                role="alertdialog"
                aria-label={request.title}
            >
                <header className="title-bar">
                    <span className="title-bar-text">
                        <span>{request.title}</span>
                    </span>
                    <button
                        type="button"
                        className="title-button"
                        aria-label={t('window.close')}
                        onClick={() => onAnswer(false)}
                    >
                        <PixelGlyph name="close" />
                    </button>
                </header>
                <div className="dialog-content">
                    <div className="dialog-icon">
                        <PixelIcon name={request.icon ?? 'info'} />
                    </div>
                    <div className="dialog-body">
                        <p>{request.message}</p>
                    </div>
                </div>
                <div className="dialog-buttons">
                    <button
                        type="button"
                        className="button button-primary"
                        autoFocus
                        onClick={() => onAnswer(true)}
                    >
                        {request.confirmLabel ?? t('dialog.ok')}
                    </button>
                    {request.cancelLabel && (
                        <button
                            type="button"
                            className="button"
                            onClick={() => onAnswer(false)}
                        >
                            {request.cancelLabel}
                        </button>
                    )}
                </div>
            </section>
        </div>
    );
}
