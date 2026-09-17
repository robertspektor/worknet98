import type { Shipment } from '@/types';

export type ShipmentStatus = 'open' | 'planned' | 'delivered';

export function shipmentStatus(shipment: Shipment): ShipmentStatus {
    if (shipment.delivered_at) {
        return 'delivered';
    }

    return shipment.plan ? 'planned' : 'open';
}
