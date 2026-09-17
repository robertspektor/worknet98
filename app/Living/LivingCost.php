<?php

namespace App\Living;

readonly class LivingCost
{
    public function __construct(
        public string $kind,
        public string $senderName,
        public string $senderAddress,
        public int $amount,
    ) {}
}
