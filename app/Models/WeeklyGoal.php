<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $employment_id
 * @property string $week
 * @property int $target
 * @property int $resolved_cases
 * @property int $bonus
 * @property CarbonImmutable $achieved_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Employment $employment
 */
#[Fillable(['employment_id', 'week', 'target', 'resolved_cases', 'bonus', 'achieved_at'])]
class WeeklyGoal extends Model
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
            'achieved_at' => 'datetime',
        ];
    }
}
