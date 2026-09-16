<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\FloppyDiskOrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $floppy_disk_id
 * @property int $price
 * @property CarbonImmutable $delivers_at
 * @property CarbonImmutable|null $delivered_at
 * @property CarbonImmutable|null $unpacked_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read User $user
 * @property-read FloppyDisk $floppyDisk
 */
#[Fillable(['user_id', 'floppy_disk_id', 'price', 'delivers_at', 'delivered_at', 'unpacked_at'])]
class FloppyDiskOrder extends Model
{
    /** @use HasFactory<FloppyDiskOrderFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<FloppyDisk, $this>
     */
    public function floppyDisk(): BelongsTo
    {
        return $this->belongsTo(FloppyDisk::class);
    }

    public function isDelivered(): bool
    {
        return $this->delivered_at !== null;
    }

    public function isUnpacked(): bool
    {
        return $this->unpacked_at !== null;
    }

    /**
     * @param  Builder<FloppyDiskOrder>  $query
     */
    public function scopeDueForDelivery(Builder $query): void
    {
        $query->whereNull('delivered_at')->where('delivers_at', '<=', now());
    }

    /**
     * @param  Builder<FloppyDiskOrder>  $query
     */
    public function scopeWaitingOnDesk(Builder $query): void
    {
        $query->whereNotNull('delivered_at')->whereNull('unpacked_at');
    }

    /**
     * @param  Builder<FloppyDiskOrder>  $query
     */
    public function scopeUnpacked(Builder $query): void
    {
        $query->whereNotNull('unpacked_at');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'delivers_at' => 'datetime',
            'delivered_at' => 'datetime',
            'unpacked_at' => 'datetime',
        ];
    }
}
