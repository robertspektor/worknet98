<?php

namespace App\Shop;

interface Product
{
    public function storefront(): Storefront;

    public function salePrice(): ?int;

    public function labelKey(): string;
}
