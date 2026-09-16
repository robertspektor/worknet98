<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\LoginLinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $email
 * @property string $token_hash
 * @property string $locale
 * @property CarbonImmutable $age_confirmed_at
 * @property CarbonImmutable $expires_at
 * @property CarbonImmutable|null $consumed_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
#[Fillable(['email', 'token_hash', 'locale', 'age_confirmed_at', 'expires_at', 'consumed_at'])]
class LoginLink extends Model
{
    /** @use HasFactory<LoginLinkFactory> */
    use HasFactory;

    /**
     * @param  Builder<LoginLink>  $query
     */
    public function scopeRedeemable(Builder $query): void
    {
        $query->whereNull('consumed_at')->where('expires_at', '>', now());
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'age_confirmed_at' => 'datetime',
            'expires_at' => 'datetime',
            'consumed_at' => 'datetime',
        ];
    }
}
