<?php

namespace App\Models;

use App\Auth\Role;
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
 * @property Role $role
 * @property CarbonImmutable $age_confirmed_at
 * @property CarbonImmutable|null $last_seen_at
 * @property string|null $remember_token
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Employment|null $employment
 */
#[Fillable(['email', 'email_verified_at', 'locale', 'age_confirmed_at', 'role', 'last_seen_at'])]
#[Hidden(['remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function isMayor(): bool
    {
        return $this->role === Role::Mayor;
    }

    /**
     * @return HasMany<LivingCostBill, $this>
     */
    public function livingCostBills(): HasMany
    {
        return $this->hasMany(LivingCostBill::class);
    }

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
        return $this->hasOne(Employment::class)->ofMany(['hired_at' => 'max'], fn ($query) => $query->whereNull('ended_at'));
    }

    /**
     * @return HasMany<DeskPlacement, $this>
     */
    public function deskPlacements(): HasMany
    {
        return $this->hasMany(DeskPlacement::class);
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
            'last_seen_at' => 'datetime',
            'role' => Role::class,
        ];
    }
}
