<?php

namespace App\Models;

use App\Metrics\FunnelStep;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property FunnelStep $name
 * @property CarbonImmutable $recorded_at
 * @property-read User $user
 */
#[Fillable(['user_id', 'name', 'recorded_at'])]
class PlayerEvent extends Model
{
    public $timestamps = false;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'name' => FunnelStep::class,
            'recorded_at' => 'datetime',
        ];
    }
}
