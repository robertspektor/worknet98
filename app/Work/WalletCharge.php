<?php

namespace App\Work;

use App\Game\ActionRefused;
use App\Models\LedgerEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class WalletCharge
{
    public function __construct(private readonly Wallet $wallet) {}

    public function charge(User $player, int $amount, LedgerReason $reason): LedgerEntry
    {
        return DB::transaction(function () use ($player, $amount, $reason): LedgerEntry {
            User::query()->whereKey($player->id)->lockForUpdate()->first();

            if ($this->wallet->balanceOf($player) < $amount) {
                throw new ActionRefused(PurchaseRefusal::InsufficientFunds);
            }

            return LedgerEntry::create([
                'user_id' => $player->id,
                'amount' => -$amount,
                'reason' => $reason,
            ]);
        });
    }
}
