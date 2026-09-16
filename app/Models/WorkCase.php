<?php

namespace App\Models;

use App\Cases\WorkCaseStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $employment_id
 * @property string $case_slug
 * @property WorkCaseStatus $status
 * @property CarbonImmutable $opened_at
 * @property CarbonImmutable|null $resolved_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Employment $employment
 */
#[Fillable(['employment_id', 'case_slug', 'status', 'opened_at', 'resolved_at'])]
class WorkCase extends Model
{
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
            'status' => WorkCaseStatus::class,
            'opened_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }
}
