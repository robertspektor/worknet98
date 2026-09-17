<?php

namespace App\Http\Controllers\Api\V1;

use App\Career\Promotions\Promotion;
use App\Http\Requests\AcceptPromotionOfferRequest;
use App\Http\Resources\PromotionOfferResource;
use App\Models\PromotionOffer;

class PromotionOfferAcceptanceController extends ApiController
{
    public function store(AcceptPromotionOfferRequest $request, PromotionOffer $promotionOffer, Promotion $promotion): PromotionOfferResource
    {
        return new PromotionOfferResource($promotion->accept($promotionOffer, $request->positionId()));
    }
}
