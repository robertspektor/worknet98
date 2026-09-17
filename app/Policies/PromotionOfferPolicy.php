<?php

namespace App\Policies;

use App\Models\PromotionOffer;
use App\Models\User;

class PromotionOfferPolicy
{
    public function respond(User $player, PromotionOffer $offer): bool
    {
        return $offer->employment->user_id === $player->id;
    }
}
