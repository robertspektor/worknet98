<?php

namespace App\Auth;

enum Role: string
{
    case Player = 'player';
    case Mayor = 'mayor';
}
