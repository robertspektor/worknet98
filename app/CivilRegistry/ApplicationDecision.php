<?php

namespace App\CivilRegistry;

enum ApplicationDecision: string
{
    case Approved = 'approved';
    case Rejected = 'rejected';
}
