<?php

namespace App\Careers\Events;

use App\Models\JobApplication;
use Illuminate\Foundation\Events\Dispatchable;

class JobApplicationSubmitted
{
    use Dispatchable;

    public function __construct(public readonly JobApplication $application) {}
}
