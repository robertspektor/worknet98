<?php

namespace App\Console\Commands;

use App\Cases\Deadlines\StaleCaseWatcher;
use App\Cases\Demand\DemandGenerator;
use App\Cases\Npc\NpcCaseWorker;
use App\Workplace\AppointmentExecutor;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('game:tick')]
#[Description('Advance the simulated branches: open new cases, hand over stale cases, let NPCs work and carry out due appointments')]
class GameTick extends Command
{
    public function handle(DemandGenerator $demand, StaleCaseWatcher $deadlines, NpcCaseWorker $npcs, AppointmentExecutor $appointments): int
    {
        $this->info("Opened {$demand->generateDue()} case(s).");
        $this->info("Handed over {$deadlines->takeOverStale()} stale case(s).");
        $this->info("Worked {$npcs->workDue()} NPC case(s).");
        $this->info("Carried out {$appointments->executeDue()} appointment(s).");

        return self::SUCCESS;
    }
}
