<?php

namespace App\Logistics;

use Carbon\CarbonImmutable;

enum Tour: string
{
    case Morning = 'morning';
    case Afternoon = 'afternoon';

    public function startsAt(CarbonImmutable $date): CarbonImmutable
    {
        return $date->startOfDay()->setTimeFromTimeString(match ($this) {
            self::Morning => '07:00',
            self::Afternoon => '13:00',
        });
    }

    public function endsAt(CarbonImmutable $date): CarbonImmutable
    {
        return $this->startsAt($date)->addHours(3);
    }
}
