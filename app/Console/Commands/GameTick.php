<?php

namespace App\Console\Commands;

use App\Career\MonthClose;
use App\Cases\Deadlines\StaleCaseWatcher;
use App\Cases\Demand\DemandGenerator;
use App\Cases\Npc\NpcCaseWorker;
use App\Workplace\AppointmentExecutor;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('game:tick')]
#[Description('Advance the simulated branches: open new cases, hand over stale cases, let NPCs work, carry out due appointments and close the month')]
class GameTick extends Command
{
    public function handle(DemandGenerator $demand, StaleCaseWatcher $deadlines, NpcCaseWorker $npcs, AppointmentExecutor $appointments, MonthClose $monthClose): int
    {
        $this->info("Opened {$demand->generateDue()} case(s).");
        $this->info("Handed over {$deadlines->takeOverStale()} stale case(s).");
        $this->info("Worked {$npcs->workDue()} NPC case(s).");
        $this->info("Carried out {$appointments->executeDue()} appointment(s).");
        $this->info("Reviewed {$monthClose->closeDue()} employment(s).");

        return self::SUCCESS;
    }
}
