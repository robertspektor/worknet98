<?php

use App\Console\Commands\ReviewJobApplications;
use Illuminate\Support\Facades\Schedule;

Schedule::command(ReviewJobApplications::class)->everyMinute()->withoutOverlapping();
