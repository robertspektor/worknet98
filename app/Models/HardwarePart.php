<?php

namespace App\Models;

use App\Hardware\HardwareSlot;
use App\Shop\Product;
use App\Shop\Storefront;
use Carbon\CarbonImmutable;
use Database\Factories\HardwarePartFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
class HardwarePart extends Model implements Product
{
    /** @use HasFactory<HardwarePartFactory> */
    use HasFactory;

    /**
     * @return MorphMany<Order, $this>
     */
    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'product');
    }

    public function storefront(): Storefront
    {
        return Storefront::ChipCity;
    }

    public function salePrice(): ?int
    {
        return $this->price;
    }

    public function labelKey(): string
    {
        return "hardware_part.{$this->slug}.label";
    }

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
    public function scopeForSale(Builder $query): void
    {
        $query->whereNotNull('price');
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
