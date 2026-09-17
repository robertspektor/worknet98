<?php

namespace App\Models;

use App\Career\ReviewRating;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $employment_id
 * @property string $period
 * @property array<string, int> $metric_totals
 * @property array<string, int> $metric_changes
 * @property int $score
 * @property ReviewRating $rating
 * @property int $bonus
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Employment $employment
 */
#[Fillable(['employment_id', 'period', 'metric_totals', 'metric_changes', 'score', 'rating', 'bonus'])]
class PerformanceReview extends Model
{
    /**
     * @return BelongsTo<Employment, $this>
     */
    public function employment(): BelongsTo
    {
        return $this->belongsTo(Employment::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metric_totals' => 'array',
            'metric_changes' => 'array',
            'rating' => ReviewRating::class,
        ];
    }
}
