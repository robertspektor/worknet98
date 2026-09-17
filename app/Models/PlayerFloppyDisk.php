<?php

namespace App\Models;

use App\FloppyDisks\DiskSource;
use Carbon\CarbonImmutable;
use Database\Factories\PlayerFloppyDiskFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property int $floppy_disk_id
 * @property int|null $order_id
 * @property DiskSource $source
 * @property string|null $label
 * @property bool $is_write_protected
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read User $user
 * @property-read FloppyDisk $floppyDisk
 */
#[Fillable(['user_id', 'floppy_disk_id', 'order_id', 'source', 'label', 'is_write_protected'])]
class PlayerFloppyDisk extends Model
{
    /** @use HasFactory<PlayerFloppyDiskFactory> */
    use HasFactory;

    public const CAPACITY_BYTES = 1_474_560;

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

    /**
     * @return HasMany<DiskFile, $this>
     */
    public function files(): HasMany
    {
        return $this->hasMany(DiskFile::class);
    }

    public function isLabelable(): bool
    {
        return $this->floppyDisk->kind->isLabelable();
    }

    public function usedBytes(): int
    {
        return (int) $this->files()->sum('size_bytes');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'source' => DiskSource::class,
            'is_write_protected' => 'boolean',
        ];
    }
}
