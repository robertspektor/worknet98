<?php

namespace App\Terminal;

use App\Models\User;
use App\World\LocalCity;

/**
 * What the public access terminal knows about whoever is signed in at it:
 * the address they signed in with and the city the terminal stands in.
 */
class ResidentCard
{
    public function __construct(private LocalCity $cities) {}

    /**
     * @return array{address: string, city: string|null}
     */
    public function of(User $player): array
    {
        return [
            'address' => $player->email,
            'city' => $this->cities->nameFor($player->locale),
        ];
    }
}
