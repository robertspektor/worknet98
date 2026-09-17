<?php

namespace App\Career\Promotions;

use App\Game\ActionRefused;
use App\Models\PromotionOffer;

class OpenPromotionOffer
{
    public function lock(PromotionOffer $offer): PromotionOffer
    {
        $locked = PromotionOffer::query()->whereKey($offer->id)->lockForUpdate()->with(['employment', 'options'])->firstOrFail();

        if (! $locked->isOpen() || $locked->employment->ended_at !== null) {
            throw new ActionRefused(PromotionRefusal::OfferClosed);
        }

        return $locked;
    }
}
