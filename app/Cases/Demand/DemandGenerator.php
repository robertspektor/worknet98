<?php

namespace App\Cases\Demand;

use App\Cases\Templates\CaseCustomers;
use App\Cases\Templates\CaseTemplateCatalog;
use App\Cases\Templates\TemplateCaseOpener;
use App\Game\GameClock;
use App\Models\Branch;
use App\Models\WorkCase;
use Illuminate\Database\UniqueConstraintViolationException;

class DemandGenerator
{
    public function __construct(
        private readonly GameClock $clock,
        private readonly CaseTemplateCatalog $templates,
        private readonly DemandPlanner $planner,
        private readonly CaseCustomers $customers,
        private readonly TemplateCaseOpener $opener,
    ) {}

    public function generateDue(): int
    {
        $opened = 0;

        foreach (Branch::with('company')->get() as $branch) {
            $opened += $this->generateFor($branch);
        }

        return $opened;
    }

    private function generateFor(Branch $branch): int
    {
        $now = $this->clock->now();
        $plan = $this->planner->plan($branch, $this->templates->forCompany($branch->company), $now->startOfDay());
        $due = array_filter($plan, fn (Demand $demand): bool => $demand->opensAt->lte($now) && ! $this->isOpened($demand));

        return count(array_filter($due, fn (Demand $demand): bool => $this->open($branch, $demand)));
    }

    private function isOpened(Demand $demand): bool
    {
        return WorkCase::query()->where('demand_key', $demand->key)->exists();
    }

    private function open(Branch $branch, Demand $demand): bool
    {
        $customer = $this->customers->randomWithoutOpenCase($branch, $demand->key);

        if ($customer === null) {
            return false;
        }

        try {
            $this->opener->open($branch, $demand->template, $customer, $demand->key);
        } catch (UniqueConstraintViolationException) {
            return false;
        }

        return true;
    }
}
