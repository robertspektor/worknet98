<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property CarbonImmutable $started_at
 * @property CarbonImmutable $last_seen_at
 * @property-read User $user
 */
#[Fillable(['user_id', 'started_at', 'last_seen_at'])]
class PlayerSession extends Model
{
    public $timestamps = false;

    public function lengthInMinutes(): int
    {
        return (int) $this->started_at->diffInMinutes($this->last_seen_at);
    }

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
            'started_at' => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }
}
