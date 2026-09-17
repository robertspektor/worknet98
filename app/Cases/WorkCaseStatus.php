<?php

namespace App\Cases;

enum WorkCaseStatus: string
{
    case Open = 'open';
    case Resolved = 'resolved';
    case Lost = 'lost';
}
