export type ChatReply = {
    slug: string;
    text: string;
};

export type ChatMessage = {
    id: number;
    contact_name: string;
    is_from_player: boolean;
    body: string;
    sent_at: string;
    is_read: boolean;
    replies: ChatReply[];
};
