<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $promotion_offer_id
 * @property int $position_id
 * @property int $daily_salary
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read PromotionOffer $offer
 * @property-read Position $position
 */
#[Fillable(['promotion_offer_id', 'position_id', 'daily_salary'])]
class PromotionOfferOption extends Model
{
    /**
     * @return BelongsTo<PromotionOffer, $this>
     */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(PromotionOffer::class, 'promotion_offer_id');
    }

    /**
     * @return BelongsTo<Position, $this>
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
}
