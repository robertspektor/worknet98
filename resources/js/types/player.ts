export type Player = {
    email: string;
    locale: string;
    employer: string | null;
    balance: number;
};

export type SessionStatus =
    | 'login-link-sent'
    | 'login-link-invalid'
    | 'signed-in'
    | null;
