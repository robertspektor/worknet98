<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\ShiftFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $employment_id
 * @property CarbonImmutable $clocked_in_at
 * @property CarbonImmutable $last_active_at
 * @property CarbonImmutable|null $clocked_out_at
 * @property int $worked_seconds
 * @property bool $clocked_out_automatically
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read User $user
 * @property-read Employment $employment
 */
#[Fillable(['user_id', 'employment_id', 'clocked_in_at', 'last_active_at', 'clocked_out_at', 'worked_seconds', 'clocked_out_automatically'])]
class Shift extends Model
{
    /** @use HasFactory<ShiftFactory> */
    use HasFactory;

    protected $attributes = [
        'worked_seconds' => 0,
        'clocked_out_automatically' => false,
    ];

    public function isOnDuty(): bool
    {
        return $this->clocked_out_at === null;
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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
            'clocked_in_at' => 'datetime',
            'last_active_at' => 'datetime',
            'clocked_out_at' => 'datetime',
            'worked_seconds' => 'integer',
            'clocked_out_automatically' => 'boolean',
        ];
    }
}
