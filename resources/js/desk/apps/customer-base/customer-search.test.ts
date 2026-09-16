import { describe, expect, it } from 'vite-plus/test';
import type { Customer } from '@/types';
import { searchCustomers } from './customer-search';

function customer(id: number, name: string, city: string): Customer {
    return {
        id,
        name,
        street: '1 Main Street',
        city,
        phone: '555-0100',
        email_address: `${id}@mail.wn`,
        notes: '',
    };
}

const customers = [
    customer(1, 'Margaret Hollis', 'Maple Falls'),
    customer(2, 'Martin Hollister', 'Maple Falls'),
    customer(3, 'Priya Raman', 'Greenvale'),
];

describe('searchCustomers', () => {
    it('returns everyone for an empty query', () => {
        expect(searchCustomers(customers, '  ')).toHaveLength(3);
    });

    it('matches names case-insensitively', () => {
        expect(searchCustomers(customers, 'HOLLIS').map((c) => c.id)).toEqual([
            1, 2,
        ]);
    });

    it('matches other fields like the city', () => {
        expect(searchCustomers(customers, 'green').map((c) => c.id)).toEqual([
            3,
        ]);
    });
});
