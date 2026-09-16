<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\InstalledProgramFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $floppy_disk_id
 * @property string $program
 * @property CarbonImmutable $installed_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read FloppyDisk $floppyDisk
 */
#[Fillable(['user_id', 'floppy_disk_id', 'program', 'installed_at'])]
class InstalledProgram extends Model
{
    /** @use HasFactory<InstalledProgramFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<FloppyDisk, $this>
     */
    public function floppyDisk(): BelongsTo
    {
        return $this->belongsTo(FloppyDisk::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'installed_at' => 'datetime',
        ];
    }
}
