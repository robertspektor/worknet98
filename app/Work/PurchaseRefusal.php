<?php

namespace App\Work;

use App\Game\Refusal;

enum PurchaseRefusal: string implements Refusal
{
    case InsufficientFunds = 'insufficient_funds';

    public function message(): string
    {
        return __('wallet.refusal.'.$this->value);
    }
}
