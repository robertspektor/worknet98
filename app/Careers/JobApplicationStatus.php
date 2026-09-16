<?php

namespace App\Careers;

enum JobApplicationStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
}
