import type { Customer } from '@/types';

export function searchCustomers(
    customers: Customer[],
    query: string,
): Customer[] {
    const needle = query.trim().toLowerCase();

    if (needle === '') {
        return customers;
    }

    return customers.filter((customer) =>
        [
            customer.name,
            customer.street,
            customer.city,
            customer.phone,
            customer.email_address,
        ].some((value) => value.toLowerCase().includes(needle)),
    );
}
