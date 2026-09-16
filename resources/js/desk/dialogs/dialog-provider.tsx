import type { ReactNode } from 'react';
import { createContext, use, useState } from 'react';
import type { IconName } from '../ui/pixel-art';
import { DialogWindow } from './dialog-window';

export type DialogRequest = {
    title: string;
    message: string;
    icon?: IconName;
    confirmLabel?: string;
    cancelLabel?: string;
};

type OpenDialog = DialogRequest & {
    id: number;
    resolve: (confirmed: boolean) => void;
};

type Dialogs = {
    confirm: (request: DialogRequest) => Promise<boolean>;
    alert: (request: Omit<DialogRequest, 'cancelLabel'>) => Promise<boolean>;
};

const DialogContext = createContext<Dialogs | null>(null);

export function DialogProvider({ children }: { children: ReactNode }) {
    const [dialogs, setDialogs] = useState<OpenDialog[]>([]);

    const show = (request: DialogRequest) =>
        new Promise<boolean>((resolve) =>
            setDialogs((current) => [
                ...current,
                { ...request, id: Date.now() + current.length, resolve },
            ]),
        );

    const answer = (dialog: OpenDialog, confirmed: boolean) => {
        setDialogs((current) =>
            current.filter((open) => open.id !== dialog.id),
        );
        dialog.resolve(confirmed);
    };

    return (
        <DialogContext value={{ confirm: show, alert: show }}>
            {children}
            {dialogs.map((dialog) => (
                <DialogWindow
                    key={dialog.id}
                    request={dialog}
                    onAnswer={(confirmed) => answer(dialog, confirmed)}
                />
            ))}
        </DialogContext>
    );
}

export function useDialogs(): Dialogs {
    const dialogs = use(DialogContext);

    if (!dialogs) {
        throw new Error('useDialogs must be used inside DialogProvider.');
    }

    return dialogs;
}
