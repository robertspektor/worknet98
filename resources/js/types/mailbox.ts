export type EmailFolder = 'inbox' | 'sent';

export type EmailAction =
    | 'confirm_appointment'
    | 'confirm_shipment'
    | 'request_details'
    | 'other';

export type MailboxScope = 'private' | 'work';

export type PromotionOfferStatus =
    | 'pending'
    | 'accepted'
    | 'declined'
    | 'expired';

export type PromotionOfferOption = {
    position_id: number;
    title: string;
    daily_salary: number;
};

export type PromotionOffer = {
    id: number;
    status: PromotionOfferStatus;
    accepted_position_id: number | null;
    options: PromotionOfferOption[];
};

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
    promotion_offer: PromotionOffer | null;
};
