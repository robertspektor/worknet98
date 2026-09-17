import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { PixelGlyph, PixelIcon } from '../ui/pixel-icon';
import type {
    DialogAnswer,
    DialogInput,
    DialogRequest,
} from './dialog-provider';

function DialogField({
    input,
    label,
    value,
    onChange,
}: {
    input: DialogInput;
    label: string;
    value: string;
    onChange: (value: string) => void;
}) {
    return (
        <label className="dialog-field">
            <input
                className="input"
                type="text"
                aria-label={label}
                value={value}
                maxLength={input.maxLength}
                spellCheck={false}
                autoFocus
                onFocus={(event) => event.target.select()}
                onChange={(event) => onChange(event.target.value)}
            />
        </label>
    );
}

export function DialogWindow({
    request,
    onAnswer,
}: {
    request: DialogRequest;
    onAnswer: (answer: DialogAnswer) => void;
}) {
    const { t } = useTranslation();
    const [value, setValue] = useState(request.input?.value ?? '');
    const answer = (confirmed: boolean) => onAnswer({ confirmed, value });

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
                        onClick={() => answer(false)}
                    >
                        <PixelGlyph name="close" />
                    </button>
                </header>
                <form
                    onSubmit={(event) => {
                        event.preventDefault();
                        answer(true);
                    }}
                >
                    <div className="dialog-content">
                        <div className="dialog-icon">
                            <PixelIcon name={request.icon ?? 'info'} />
                        </div>
                        <div className="dialog-body">
                            <p>{request.message}</p>
                            {request.input && (
                                <DialogField
                                    input={request.input}
                                    label={request.message}
                                    value={value}
                                    onChange={setValue}
                                />
                            )}
                        </div>
                    </div>
                    <div className="dialog-buttons">
                        <button
                            type="submit"
                            className="button button-primary"
                            autoFocus={!request.input}
                        >
                            {request.confirmLabel ?? t('dialog.ok')}
                        </button>
                        {request.cancelLabel && (
                            <button
                                type="button"
                                className="button"
                                onClick={() => answer(false)}
                            >
                                {request.cancelLabel}
                            </button>
                        )}
                    </div>
                </form>
            </section>
        </div>
    );
}
