import { describe, expect, it } from 'vite-plus/test';
import type { Colleague, Customer } from '@/types';
import { recipientFields, recipientsOf } from './compose-recipients';

const customer: Customer = {
    id: 4,
    name: 'Margaret Hollis',
    street: '14 Birch Lane',
    city: 'Maple Falls',
    phone: '555-0142',
    email_address: 'm.hollis@mail.wn',
    notes: 'Has a loud dog.',
};

const colleague: Colleague = {
    position_id: 7,
    name: 'Bev Mercer',
    title: 'Office Coordinator',
    address: 'bev.mercer@flowright.wn',
};

describe('recipientsOf', () => {
    it('offers customers and colleagues with their role', () => {
        expect(recipientsOf([customer], [colleague])).toEqual([
            {
                value: 'customer:4',
                label: 'Margaret Hollis',
                address: 'm.hollis@mail.wn',
            },
            {
                value: 'colleague:7',
                label: 'Bev Mercer (Office Coordinator)',
                address: 'bev.mercer@flowright.wn',
            },
        ]);
    });
});

describe('recipientFields', () => {
    it('sends customers and colleagues under their own key', () => {
        expect(recipientFields('customer:4')).toEqual({ customer_id: 4 });
        expect(recipientFields('colleague:7')).toEqual({
            colleague_position_id: 7,
        });
    });
});
