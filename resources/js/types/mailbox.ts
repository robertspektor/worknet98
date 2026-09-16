export type EmailFolder = 'inbox' | 'sent';

export type EmailAction = 'confirm_appointment' | 'request_details' | 'other';

export type MailboxScope = 'private' | 'work';

export type Email = {
    id: number;
    sender_name: string;
    sender_address: string;
    subject: string;
    body: string;
    received_at: string;
    is_read: boolean;
    folder: EmailFolder;
    recipient_name: string | null;
    recipient_address: string | null;
    action: EmailAction | null;
};
