<?php

namespace App\Shop;

use App\Game\GameClock;
use App\Mailbox\EmailDraft;
use App\Models\Order;

class OrderConfirmation
{
    private const DELIVERY_FORMAT = 'dddd, LL, HH:mm';

    public function __construct(private readonly GameClock $clock) {}

    public function compose(Order $order): EmailDraft
    {
        $locale = $order->user->locale;
        $storefront = $order->product->storefront();
        $replacements = [
            'item' => __($order->product->labelKey(), [], $locale),
            'price' => $order->price,
            'number' => $this->orderNumber($order),
            'delivery' => $this->clock->fromReal($order->delivers_at)
                ->settings(['locale' => $locale])
                ->isoFormat(self::DELIVERY_FORMAT),
        ];

        return new EmailDraft(
            senderName: $storefront->senderName(),
            senderAddress: $storefront->senderAddress(),
            subject: __("game_mail.order.{$storefront->value}.subject", $replacements, $locale),
            body: __("game_mail.order.{$storefront->value}.body", $replacements, $locale),
        );
    }

    private function orderNumber(Order $order): string
    {
        return sprintf('%s-%06d', mb_strtoupper(mb_substr($order->product->storefront()->value, 0, 2)), $order->id);
    }
}
