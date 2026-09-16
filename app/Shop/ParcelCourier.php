<?php

namespace App\Shop;

use App\Mailbox\Mailbox;
use App\Models\FloppyDiskOrder;
use Illuminate\Support\Facades\DB;

class ParcelCourier
{
    public function __construct(
        private readonly ShippingNotice $shippingNotice,
        private readonly Mailbox $mailbox,
    ) {}

    public function deliverDue(): int
    {
        $delivered = 0;

        FloppyDiskOrder::query()
            ->dueForDelivery()
            ->with(['user', 'floppyDisk'])
            ->lazyById()
            ->each(function (FloppyDiskOrder $order) use (&$delivered): void {
                if (DB::transaction(fn (): bool => $this->deliverIfDue($order))) {
                    $delivered++;
                }
            });

        return $delivered;
    }

    private function deliverIfDue(FloppyDiskOrder $order): bool
    {
        $isDue = FloppyDiskOrder::query()->whereKey($order->id)->dueForDelivery()->lockForUpdate()->exists();

        if (! $isDue) {
            return false;
        }

        $order->update(['delivered_at' => now()]);
        $this->mailbox->deliver($order->user, $this->shippingNotice->compose($order));

        return true;
    }
}
