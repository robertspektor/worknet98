<?php

namespace App\Shop;

use App\Game\ActionRefused;
use App\Models\FloppyDisk;
use App\Models\FloppyDiskOrder;
use App\Models\User;
use App\Work\LedgerReason;
use App\Work\WalletCharge;
use Illuminate\Support\Facades\DB;

class DiskOrderPlacer
{
    public function __construct(
        private readonly WalletCharge $walletCharge,
        private readonly ParcelSchedule $parcelSchedule,
    ) {}

    public function place(User $player, FloppyDisk $disk): FloppyDiskOrder
    {
        $price = $disk->price ?? throw new ActionRefused(ShopRefusal::NotForSale);

        return DB::transaction(function () use ($player, $disk, $price): FloppyDiskOrder {
            $this->refuseRepeatedOrder($player, $disk);
            $this->walletCharge->charge($player, $price, LedgerReason::Purchase);

            return FloppyDiskOrder::create([
                'user_id' => $player->id,
                'floppy_disk_id' => $disk->id,
                'price' => $price,
                'delivers_at' => $this->parcelSchedule->deliveryTime(),
            ]);
        });
    }

    private function refuseRepeatedOrder(User $player, FloppyDisk $disk): void
    {
        $isOrdered = FloppyDiskOrder::query()
            ->where('user_id', $player->id)
            ->where('floppy_disk_id', $disk->id)
            ->exists();

        if ($isOrdered) {
            throw new ActionRefused(ShopRefusal::AlreadyOrdered);
        }
    }
}
