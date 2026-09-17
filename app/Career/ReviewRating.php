<?php

namespace App\Career;

enum ReviewRating: string
{
    private const EXCELLENT_FROM = 3;

    case Excellent = 'excellent';
    case Solid = 'solid';
    case Poor = 'poor';

    public static function forScore(int $score): self
    {
        return match (true) {
            $score >= self::EXCELLENT_FROM => self::Excellent,
            $score >= 0 => self::Solid,
            default => self::Poor,
        };
    }
}
