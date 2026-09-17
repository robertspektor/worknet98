<?php

namespace App\Workplace;

enum Availability: string
{
    private const NOON = '12:00';

    case Morning = 'morning';
    case Afternoon = 'afternoon';
    case Any = 'any';

    public function includes(string $slot): bool
    {
        return match ($this) {
            self::Morning => $slot < self::NOON,
            self::Afternoon => $slot >= self::NOON,
            self::Any => true,
        };
    }
}
