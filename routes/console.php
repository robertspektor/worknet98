<?php

use App\Console\Commands\DeliverParcels;
use App\Console\Commands\GameTick;
use App\Console\Commands\ReviewJobApplications;
use Illuminate\Support\Facades\Schedule;

Schedule::command(ReviewJobApplications::class)->everyMinute()->withoutOverlapping();
Schedule::command(DeliverParcels::class)->everyMinute()->withoutOverlapping();
Schedule::command(GameTick::class)->everyMinute()->withoutOverlapping();
