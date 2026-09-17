<?php

namespace App\Career\Promotions;

use App\Game\Refusal;

enum PromotionRefusal: string implements Refusal
{
    case OfferClosed = 'offer_closed';
    case PositionNotOffered = 'position_not_offered';
    case PositionTaken = 'position_taken';

    public function message(): string
    {
        return __('career.refusal.'.$this->value);
    }
}
