<?php

namespace App\Milestones\Conditions;

use App\Models\User;
use App\Work\Wallet;

readonly class Balance implements MilestoneCondition
{
    public function __construct(private Wallet $wallet, private int $amount) {}

    public function isMetBy(User $player): bool
    {
        return $this->wallet->balanceOf($player) >= $this->amount;
    }
}
