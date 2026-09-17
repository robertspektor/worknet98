<?php

namespace App\Models;

use App\FloppyDisks\FloppyDiskKind;
use App\Shop\Product;
use App\Shop\Storefront;
use Carbon\CarbonImmutable;
use Database\Factories\FloppyDiskFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int $id
 * @property string $slug
 * @property FloppyDiskKind $kind
 * @property string $color
 * @property string|null $program
 * @property bool $is_starter
 * @property int|null $price
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
#[Fillable(['slug', 'kind', 'color', 'program', 'is_starter', 'price'])]
class FloppyDisk extends Model implements Product
{
    /** @use HasFactory<FloppyDiskFactory> */
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
        return Storefront::DiskDepot;
    }

    public function salePrice(): ?int
    {
        return $this->price;
    }

    public function labelKey(): string
    {
        return "floppy_disk.{$this->slug}.label";
    }

    public function isInstallable(): bool
    {
        return $this->program !== null;
    }

    /**
     * @param  Builder<FloppyDisk>  $query
     */
    public function scopeStarter(Builder $query): void
    {
        $query->where('is_starter', true);
    }

    /**
     * @param  Builder<FloppyDisk>  $query
     */
    public function scopeForSale(Builder $query): void
    {
        $query->whereNotNull('price');
    }

    public function isForSale(): bool
    {
        return $this->price !== null;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kind' => FloppyDiskKind::class,
            'is_starter' => 'boolean',
        ];
    }
}
