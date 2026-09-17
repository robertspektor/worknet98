<?php

namespace App\Shop;

use App\Mailbox\EmailDraft;
use App\Models\Order;

class ShippingNotice
{
    public function compose(Order $order): EmailDraft
    {
        $locale = $order->user->locale;
        $storefront = $order->product->storefront();
        $replacements = [
            'item' => __($order->product->labelKey(), [], $locale),
            'price' => $order->price,
        ];

        return new EmailDraft(
            senderName: $storefront->senderName(),
            senderAddress: $storefront->senderAddress(),
            subject: __("game_mail.parcel.{$storefront->value}.subject", $replacements, $locale),
            body: __("game_mail.parcel.{$storefront->value}.body", $replacements, $locale),
        );
    }
}
