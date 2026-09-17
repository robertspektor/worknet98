import type { ReactNode } from 'react';
import { useState } from 'react';
import { createDeskContext } from '../state/create-desk-context';
import type { IconName } from '../ui/pixel-art';
import { DialogWindow } from './dialog-window';

export type DialogInput = {
    value: string;
    maxLength: number;
};

export type DialogRequest = {
    title: string;
    message: string;
    icon?: IconName;
    confirmLabel?: string;
    cancelLabel?: string;
    input?: DialogInput;
};

export type DialogAnswer = {
    confirmed: boolean;
    value: string;
};

type OpenDialog = DialogRequest & {
    id: number;
    resolve: (answer: DialogAnswer) => void;
};

type Dialogs = {
    confirm: (request: DialogRequest) => Promise<boolean>;
    alert: (request: Omit<DialogRequest, 'cancelLabel'>) => Promise<boolean>;
    prompt: (
        request: DialogRequest & { input: DialogInput },
    ) => Promise<string | null>;
};

const { Context, useRequired } = createDeskContext<Dialogs>('Dialogs');

export function DialogProvider({ children }: { children: ReactNode }) {
    const [dialogs, setDialogs] = useState<OpenDialog[]>([]);

    const show = (request: DialogRequest) =>
        new Promise<DialogAnswer>((resolve) =>
            setDialogs((current) => [
                ...current,
                { ...request, id: Date.now() + current.length, resolve },
            ]),
        );

    const confirm = (request: DialogRequest) =>
        show(request).then(({ confirmed }) => confirmed);

    const prompt = (request: DialogRequest) =>
        show(request).then(({ confirmed, value }) =>
            confirmed ? value : null,
        );

    const answer = (dialog: OpenDialog, result: DialogAnswer) => {
        setDialogs((current) =>
            current.filter((open) => open.id !== dialog.id),
        );
        dialog.resolve(result);
    };

    return (
        <Context value={{ confirm, alert: confirm, prompt }}>
            {children}
            {dialogs.map((dialog) => (
                <DialogWindow
                    key={dialog.id}
                    request={dialog}
                    onAnswer={(result) => answer(dialog, result)}
                />
            ))}
        </Context>
    );
}

export const useDialogs = useRequired;
