import type { PromotionOffer } from '@/types';

export type OfferOutcome = {
    key: string;
    replacements: Record<string, string>;
};

export function offerOutcome(offer: PromotionOffer): OfferOutcome | null {
    switch (offer.status) {
        case 'pending':
            return null;
        case 'accepted':
            return {
                key: 'inbox.promotion.accepted',
                replacements: {
                    position:
                        offer.options.find(
                            (option) =>
                                option.position_id ===
                                offer.accepted_position_id,
                        )?.title ?? '',
                },
            };
        default:
            return { key: `inbox.promotion.${offer.status}`, replacements: {} };
    }
}
