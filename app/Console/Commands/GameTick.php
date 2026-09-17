<?php

namespace App\Console\Commands;

use App\Career\MonthClose;
use App\Cases\Deadlines\StaleCaseWatcher;
use App\Cases\Npc\NpcCaseWorker;
use App\CivilRegistry\NpcRegistrar;
use App\Living\MonthlyBills;
use App\Logistics\NpcDispatcher;
use App\Logistics\Supply\SupplyOrderGenerator;
use App\Logistics\TourExecutor;
use App\Work\IdleShiftCloser;
use App\Workplace\AppointmentExecutor;
use App\World\Events\WorldEventGenerator;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('game:tick')]
#[Description('Advance the simulated world: clock out idle players, record world events and open their cases, place supply orders between companies, hand over stale cases, let NPCs work and decide applications, deliver shipments, carry out due appointments close the month and charge the living costs')]
class GameTick extends Command
{
    public function handle(IdleShiftCloser $idleShifts, WorldEventGenerator $worldEvents, StaleCaseWatcher $deadlines, NpcCaseWorker $npcs, SupplyOrderGenerator $supplyOrders, NpcDispatcher $dispatchers, NpcRegistrar $registrars, TourExecutor $tours, AppointmentExecutor $appointments, MonthClose $monthClose, MonthlyBills $bills): int
    {
        $this->info("Clocked out {$idleShifts->closeIdle()} idle shift(s).");
        $this->info("Recorded {$worldEvents->generateDue()} world event(s).");
        $this->info("Placed {$supplyOrders->generateDue()} supply order(s).");
        $this->info("Handed over {$deadlines->takeOverStale()} stale case(s).");
        $this->info("Worked {$npcs->workDue()} NPC case(s).");
        $this->info("Planned {$dispatchers->workDue()} NPC shipment(s).");
        $this->info("Decided {$registrars->workDue()} NPC application(s).");
        $this->info("Delivered {$tours->executeDue()} shipment(s).");
        $this->info("Carried out {$appointments->executeDue()} appointment(s).");
        $this->info("Reviewed {$monthClose->closeDue()} employment(s).");
        $this->info("Billed {$bills->chargeDue()} player(s) for living costs.");

        return self::SUCCESS;
    }
}
