<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $email
 * @property CarbonImmutable|null $email_verified_at
 * @property string $locale
 * @property CarbonImmutable $age_confirmed_at
 * @property string|null $remember_token
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Employment|null $employment
 */
#[Fillable(['email', 'email_verified_at', 'locale', 'age_confirmed_at'])]
#[Hidden(['remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @return HasMany<JobApplication, $this>
     */
    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * @return HasOne<Employment, $this>
     */
    public function employment(): HasOne
    {
        return $this->hasOne(Employment::class)->latestOfMany('hired_at');
    }

    /**
     * @return HasMany<Email, $this>
     */
    public function emails(): HasMany
    {
        return $this->hasMany(Email::class);
    }

    /**
     * @return HasMany<InstalledProgram, $this>
     */
    public function installedPrograms(): HasMany
    {
        return $this->hasMany(InstalledProgram::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'age_confirmed_at' => 'datetime',
        ];
    }
}
