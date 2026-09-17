import { describe, expect, it } from 'vite-plus/test';
import type { PromotionOffer } from '@/types';
import { offerOutcome } from './promotion-offer-outcome';

const offer: PromotionOffer = {
    id: 1,
    status: 'pending',
    accepted_position_id: null,
    options: [
        { position_id: 7, title: 'Emergency Dispatcher', daily_salary: 130 },
        {
            position_id: 8,
            title: 'Service Contract Planner',
            daily_salary: 120,
        },
    ],
};

describe('offerOutcome', () => {
    it('has no outcome while the offer is pending', () => {
        expect(offerOutcome(offer)).toBeNull();
    });

    it('names the accepted position', () => {
        expect(
            offerOutcome({
                ...offer,
                status: 'accepted',
                accepted_position_id: 8,
            }),
        ).toEqual({
            key: 'inbox.promotion.accepted',
            replacements: { position: 'Service Contract Planner' },
        });
    });

    it('reports a declined or expired offer', () => {
        expect(offerOutcome({ ...offer, status: 'expired' })?.key).toBe(
            'inbox.promotion.expired',
        );
    });
});
