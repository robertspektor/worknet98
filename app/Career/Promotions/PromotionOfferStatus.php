<?php

namespace App\Career\Promotions;

enum PromotionOfferStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Declined = 'declined';
}
