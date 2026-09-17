import type { Colleague, Customer } from '@/types';

export type Recipient = {
    value: string;
    label: string;
    address: string;
};

export function recipientsOf(
    customers: Customer[],
    colleagues: Colleague[],
): Recipient[] {
    return [
        ...customers.map((customer) => ({
            value: `customer:${customer.id}`,
            label: customer.name,
            address: customer.email_address,
        })),
        ...colleagues.map((colleague) => ({
            value: `colleague:${colleague.position_id}`,
            label: `${colleague.name} (${colleague.title})`,
            address: colleague.address,
        })),
    ];
}

export function recipientFields(
    value: string,
): { customer_id: number } | { colleague_position_id: number } {
    const [kind, id] = value.split(':');

    return kind === 'colleague'
        ? { colleague_position_id: Number(id) }
        : { customer_id: Number(id) };
}
