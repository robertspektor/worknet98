<?php

namespace App\Shop;

use App\Mailbox\EmailDraft;
use App\Models\FloppyDiskOrder;

class ShippingNotice
{
    public function compose(FloppyDiskOrder $order): EmailDraft
    {
        $locale = $order->user->locale;
        $replacements = [
            'disk' => __("floppy_disk.{$order->floppyDisk->slug}.label", [], $locale),
            'price' => $order->price,
        ];

        return new EmailDraft(
            senderName: 'DiskDepot',
            senderAddress: 'orders@diskdepot.wn',
            subject: __('game_mail.parcel.subject', $replacements, $locale),
            body: __('game_mail.parcel.body', $replacements, $locale),
        );
    }
}
