<?php

namespace App\Models;

use App\FloppyDisks\DiskFileKind;
use App\FloppyDisks\DiskText;
use Carbon\CarbonImmutable;
use Database\Factories\DiskFileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $player_floppy_disk_id
 * @property string $name
 * @property DiskFileKind $kind
 * @property string|null $body
 * @property string|null $content_key
 * @property string|null $program
 * @property int $size_bytes
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read PlayerFloppyDisk $disk
 */
#[Fillable(['player_floppy_disk_id', 'name', 'kind', 'body', 'content_key', 'program', 'size_bytes'])]
class DiskFile extends Model
{
    /** @use HasFactory<DiskFileFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<PlayerFloppyDisk, $this>
     */
    public function disk(): BelongsTo
    {
        return $this->belongsTo(PlayerFloppyDisk::class, 'player_floppy_disk_id');
    }

    public function isInstallable(): bool
    {
        return $this->kind === DiskFileKind::Setup && $this->program !== null;
    }

    public function textIn(string $locale): ?string
    {
        return $this->content_key === null ? $this->body : DiskText::translate($this->content_key, $locale);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kind' => DiskFileKind::class,
        ];
    }
}
