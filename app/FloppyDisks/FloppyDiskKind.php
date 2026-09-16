<?php

namespace App\FloppyDisks;

enum FloppyDiskKind: string
{
    case Program = 'program';
    case Data = 'data';
    case Story = 'story';
    case Game = 'game';
}
