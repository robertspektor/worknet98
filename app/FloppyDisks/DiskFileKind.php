<?php

namespace App\FloppyDisks;

enum DiskFileKind: string
{
    case Text = 'text';
    case Setup = 'setup';
}
