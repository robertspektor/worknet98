<?php

namespace App\Http\Controllers\Api\V1;

use App\Career\Promotions\PromotionDecline;
use App\Http\Resources\PromotionOfferResource;
use App\Models\PromotionOffer;

class PromotionOfferDeclineController extends ApiController
{
    public function store(PromotionOffer $promotionOffer, PromotionDecline $decline): PromotionOfferResource
    {
        return new PromotionOfferResource($decline->decline($promotionOffer));
    }
}
