<?php

namespace App\Console\Commands;

use App\Cases\Npc\NpcCaseWorker;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('cases:work-npc-cases')]
#[Description('Let NPCs work on the cases routed to their positions once they are due')]
class WorkNpcCases extends Command
{
    public function handle(NpcCaseWorker $worker): int
    {
        $this->info("Worked {$worker->workDue()} NPC case(s).");

        return self::SUCCESS;
    }
}
