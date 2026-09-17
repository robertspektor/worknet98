<?php

namespace App\Shop;

use App\Game\ActionRefused;
use App\Mailbox\Mailbox;
use App\Models\Order;
use App\Models\User;
use App\Work\LedgerReason;
use App\Work\WalletCharge;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderPlacer
{
    public function __construct(
        private readonly WalletCharge $walletCharge,
        private readonly ParcelSchedule $parcelSchedule,
        private readonly OrderConfirmation $orderConfirmation,
        private readonly Mailbox $mailbox,
    ) {}

    public function place(User $player, Product&Model $product): Order
    {
        $price = $product->salePrice() ?? throw new ActionRefused(ShopRefusal::NotForSale);

        return DB::transaction(function () use ($player, $product, $price): Order {
            $this->refuseRepeatedOrder($player, $product);
            $this->walletCharge->charge($player, $price, LedgerReason::Purchase);

            $order = Order::create([
                'user_id' => $player->id,
                'product_type' => $product->getMorphClass(),
                'product_id' => $product->getKey(),
                'price' => $price,
                'delivers_at' => $this->parcelSchedule->deliveryTime(),
            ]);
            $this->mailbox->deliver($player, $this->orderConfirmation->compose($order));

            return $order;
        });
    }

    private function refuseRepeatedOrder(User $player, Product&Model $product): void
    {
        $isOrdered = Order::query()
            ->where('user_id', $player->id)
            ->whereMorphedTo('product', $product)
            ->exists();

        if ($isOrdered) {
            throw new ActionRefused(ShopRefusal::AlreadyOrdered);
        }
    }
}
