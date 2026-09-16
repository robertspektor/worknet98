<?php

namespace App\Auth\LoginLinks;

use App\Mail\LoginLinkMail;
use App\Models\LoginLink;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LoginLinkIssuer
{
    private const TOKEN_LENGTH = 64;

    public function issue(string $email, string $locale): void
    {
        $token = Str::random(self::TOKEN_LENGTH);

        LoginLink::create([
            'email' => Str::lower($email),
            'token_hash' => TokenHash::of($token),
            'locale' => $locale,
            'age_confirmed_at' => now(),
            'expires_at' => now()->addMinutes($this->lifetimeMinutes()),
        ]);

        Mail::to($email)
            ->locale($locale)
            ->send(new LoginLinkMail(route('login.show', $token), $this->lifetimeMinutes()));
    }

    private function lifetimeMinutes(): int
    {
        /** @var int */
        return config('login_links.lifetime_minutes');
    }
}
