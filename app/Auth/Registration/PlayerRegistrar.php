<?php

namespace App\Auth\Registration;

use App\Models\User;
use Illuminate\Support\Str;

class PlayerRegistrar
{
    public function register(string $email, string $locale): ?User
    {
        $player = User::createOrFirst(['email' => Str::lower($email)], [
            'locale' => $locale,
            'age_confirmed_at' => now(),
        ]);

        return $player->wasRecentlyCreated ? $player : null;
    }
}
