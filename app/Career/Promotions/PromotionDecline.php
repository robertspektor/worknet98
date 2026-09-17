<?php

namespace App\Career\Promotions;

use App\Models\PromotionOffer;
use Illuminate\Support\Facades\DB;

class PromotionDecline
{
    public function __construct(private readonly OpenPromotionOffer $openOffer) {}

    public function decline(PromotionOffer $offer): PromotionOffer
    {
        return DB::transaction(function () use ($offer): PromotionOffer {
            $offer = $this->openOffer->lock($offer);
            $offer->update(['status' => PromotionOfferStatus::Declined, 'responded_at' => now()]);

            return $offer;
        });
    }
}
