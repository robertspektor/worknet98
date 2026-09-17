<?php

namespace App\World\Population;

enum Occupation: string
{
    case Employed = 'employed';
    case Unemployed = 'unemployed';
    case Retired = 'retired';
    case InTraining = 'in_training';

    public function needsProfession(): bool
    {
        return $this === self::Employed || $this === self::InTraining;
    }
}
