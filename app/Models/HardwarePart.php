<?php

namespace App\Models;

use App\Hardware\HardwareSlot;
use Carbon\CarbonImmutable;
use Database\Factories\HardwarePartFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $slug
 * @property HardwareSlot $slot
 * @property int $speed_mhz
 * @property bool $is_starter
 * @property int|null $price
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
#[Fillable(['slug', 'slot', 'speed_mhz', 'is_starter', 'price'])]
class HardwarePart extends Model
{
    /** @use HasFactory<HardwarePartFactory> */
    use HasFactory;

    /**
     * @param  Builder<HardwarePart>  $query
     */
    public function scopeStarter(Builder $query): void
    {
        $query->where('is_starter', true);
    }

    /**
     * @param  Builder<HardwarePart>  $query
     */
    public function scopeForSlot(Builder $query, HardwareSlot $slot): void
    {
        $query->where('slot', $slot);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'slot' => HardwareSlot::class,
            'is_starter' => 'boolean',
        ];
    }
}
