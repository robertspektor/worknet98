<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $slug
 * @property string $locale
 * @property string $name
 * @property string $industry
 * @property string $tagline
 * @property string $description
 * @property string $hr_contact_name
 * @property string $hr_contact_address
 * @property string $hiring_note
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
#[Fillable(['slug', 'locale', 'name', 'industry', 'tagline', 'description', 'hr_contact_name', 'hr_contact_address', 'hiring_note'])]
class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory;

    /**
     * @return HasMany<JobOpening, $this>
     */
    public function jobOpenings(): HasMany
    {
        return $this->hasMany(JobOpening::class);
    }

    /**
     * @return HasMany<Branch, $this>
     */
    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }
}
