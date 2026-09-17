<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $company_id
 * @property int $author_position_id
 * @property int|null $author_employment_id
 * @property string|null $slug
 * @property string $title
 * @property CarbonImmutable $last_posted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Company $company
 * @property-read Position $authorPosition
 * @property-read Employment|null $authorEmployment
 */
#[Fillable(['company_id', 'author_position_id', 'author_employment_id', 'slug', 'title', 'last_posted_at'])]
class ForumThread extends Model
{
    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<Position, $this>
     */
    public function authorPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'author_position_id');
    }

    /**
     * @return BelongsTo<Employment, $this>
     */
    public function authorEmployment(): BelongsTo
    {
        return $this->belongsTo(Employment::class, 'author_employment_id');
    }

    /**
     * @return HasMany<ForumPost, $this>
     */
    public function posts(): HasMany
    {
        return $this->hasMany(ForumPost::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_posted_at' => 'datetime',
        ];
    }
}
