<?php

namespace App\Models;

use App\Career\Promotions\PromotionOfferStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $employment_id
 * @property int $performance_review_id
 * @property int|null $email_id
 * @property PromotionOfferStatus $status
 * @property int|null $accepted_position_id
 * @property CarbonImmutable $expires_at
 * @property CarbonImmutable|null $responded_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Employment $employment
 * @property-read PerformanceReview $performanceReview
 * @property-read Collection<int, PromotionOfferOption> $options
 */
#[Fillable(['employment_id', 'performance_review_id', 'email_id', 'status', 'accepted_position_id', 'expires_at', 'responded_at'])]
class PromotionOffer extends Model
{
    public function isOpen(): bool
    {
        return $this->status === PromotionOfferStatus::Pending && $this->expires_at->isFuture();
    }

    /**
     * @return BelongsTo<Employment, $this>
     */
    public function employment(): BelongsTo
    {
        return $this->belongsTo(Employment::class);
    }

    /**
     * @return BelongsTo<PerformanceReview, $this>
     */
    public function performanceReview(): BelongsTo
    {
        return $this->belongsTo(PerformanceReview::class);
    }

    /**
     * @return HasMany<PromotionOfferOption, $this>
     */
    public function options(): HasMany
    {
        return $this->hasMany(PromotionOfferOption::class)->orderBy('id');
    }

    /**
     * @param  Builder<PromotionOffer>  $query
     */
    public function scopeOpen(Builder $query): void
    {
        $query->where('status', PromotionOfferStatus::Pending)->where('expires_at', '>', now());
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PromotionOfferStatus::class,
            'expires_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }
}
