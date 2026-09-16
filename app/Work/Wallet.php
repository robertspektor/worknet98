<?php

namespace App\Work;

use App\Models\LedgerEntry;
use App\Models\User;

class Wallet
{
    public function balanceOf(User $player): int
    {
        return (int) LedgerEntry::query()->where('user_id', $player->id)->sum('amount');
    }
}
