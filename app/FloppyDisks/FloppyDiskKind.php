<?php

namespace App\FloppyDisks;

enum FloppyDiskKind: string
{
    case Program = 'program';
    case Data = 'data';
    case Story = 'story';
    case Game = 'game';
    case Blank = 'blank';

    public function isWriteProtectedOnDelivery(): bool
    {
        return match ($this) {
            self::Program, self::Story, self::Game => true,
            self::Data, self::Blank => false,
        };
    }

    public function isLabelable(): bool
    {
        return $this === self::Blank;
    }
}
