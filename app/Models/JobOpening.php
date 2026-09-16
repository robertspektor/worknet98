<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\JobOpeningFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $company_id
 * @property string $slug
 * @property string $title
 * @property string $description
 * @property int $daily_salary
 * @property bool $is_open
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Company $company
 */
#[Fillable(['company_id', 'slug', 'title', 'description', 'daily_salary', 'is_open'])]
class JobOpening extends Model
{
    /** @use HasFactory<JobOpeningFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_open' => 'boolean',
        ];
    }
}
