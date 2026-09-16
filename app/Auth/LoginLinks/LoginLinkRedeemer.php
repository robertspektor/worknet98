<?php

namespace App\Auth\LoginLinks;

use App\Models\LoginLink;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoginLinkRedeemer
{
    public function redeem(string $token): ?User
    {
        return DB::transaction(function () use ($token): ?User {
            $link = LoginLink::query()
                ->redeemable()
                ->where('token_hash', TokenHash::of($token))
                ->lockForUpdate()
                ->first();

            if ($link === null) {
                return null;
            }

            $link->update(['consumed_at' => now()]);

            return $this->playerFor($link);
        });
    }

    private function playerFor(LoginLink $link): User
    {
        $player = User::firstOrCreate(['email' => $link->email], [
            'locale' => $link->locale,
            'age_confirmed_at' => $link->age_confirmed_at,
        ]);

        if ($player->email_verified_at === null) {
            $player->update(['email_verified_at' => now()]);
        }

        return $player;
    }
}
