<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\PlayerHardwarePartFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $hardware_part_id
 * @property CarbonImmutable|null $installed_at
 * @property CarbonImmutable|null $removed_at
 * @property bool $needs_thermal_paste
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read HardwarePart $hardwarePart
 */
#[Fillable(['user_id', 'hardware_part_id', 'installed_at', 'removed_at', 'needs_thermal_paste'])]
class PlayerHardwarePart extends Model
{
    /** @use HasFactory<PlayerHardwarePartFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<HardwarePart, $this>
     */
    public function hardwarePart(): BelongsTo
    {
        return $this->belongsTo(HardwarePart::class);
    }

    public function isUsed(): bool
    {
        return $this->removed_at !== null;
    }

    /**
     * @param  Builder<PlayerHardwarePart>  $query
     */
    public function scopeInstalled(Builder $query): void
    {
        $query->whereNotNull('installed_at');
    }

    /**
     * @param  Builder<PlayerHardwarePart>  $query
     */
    public function scopeOnDesk(Builder $query): void
    {
        $query->whereNull('installed_at');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'installed_at' => 'datetime',
            'removed_at' => 'datetime',
            'needs_thermal_paste' => 'boolean',
        ];
    }
}
