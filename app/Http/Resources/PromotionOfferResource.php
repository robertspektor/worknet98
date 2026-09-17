<?php

namespace App\Http\Resources;

use App\Career\Promotions\PromotionOfferStatus;
use App\Models\PromotionOffer;
use App\Models\PromotionOfferOption;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PromotionOffer
 */
class PromotionOfferResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->displayStatus(),
            'accepted_position_id' => $this->accepted_position_id,
            'options' => $this->options->map(fn (PromotionOfferOption $option): array => [
                'position_id' => $option->position_id,
                'title' => $option->position->title,
                'daily_salary' => $option->daily_salary,
            ])->all(),
        ];
    }

    private function displayStatus(): string
    {
        return $this->status === PromotionOfferStatus::Pending && ! $this->isOpen() ? 'expired' : $this->status->value;
    }
}
