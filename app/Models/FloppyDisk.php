<?php

namespace App\Models;

use App\FloppyDisks\FloppyDiskKind;
use Carbon\CarbonImmutable;
use Database\Factories\FloppyDiskFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $slug
 * @property FloppyDiskKind $kind
 * @property string $color
 * @property string|null $program
 * @property bool $is_starter
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
#[Fillable(['slug', 'kind', 'color', 'program', 'is_starter'])]
class FloppyDisk extends Model
{
    /** @use HasFactory<FloppyDiskFactory> */
    use HasFactory;

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
