import type { CompanySoftware, EmailAction } from '@/types';

export function actionsFor(software: CompanySoftware | null): EmailAction[] {
    const confirmations: EmailAction[] = [
        ...(software?.app_names.scheduler
            ? ['confirm_appointment' as const]
            : []),
        ...(software?.app_names.tours ? ['confirm_shipment' as const] : []),
    ];

    return [...confirmations, 'request_details', 'other'];
}
