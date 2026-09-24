<?php

namespace App\Auth\LoginLinks;

use App\Models\User;
use Illuminate\Support\Str;

/**
 * The sign-in branch of the terminal only ever reaches players who are
 * already registered, and never tells the visitor which addresses those are.
 */
class ReturningPlayerLink
{
    public function __construct(private LoginLinkIssuer $issuer) {}

    public function issue(string $email, string $locale): void
    {
        if (! User::query()->where('email', Str::lower($email))->exists()) {
            return;
        }

        $this->issuer->issue($email, $locale);
    }
}
