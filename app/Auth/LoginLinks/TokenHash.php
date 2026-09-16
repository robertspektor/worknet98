<?php

namespace App\Auth\LoginLinks;

class TokenHash
{
    public static function of(string $token): string
    {
        return hash('sha256', $token);
    }
}
