<?php

namespace App\Shop;

use App\Game\Refusal;

enum ShopRefusal: string implements Refusal
{
    case NotForSale = 'not_for_sale';
    case AlreadyOrdered = 'already_ordered';

    public function message(): string
    {
        return __('shop.refusal.'.$this->value);
    }
}
