<?php

namespace App\Careers;

use App\Cases\CaseCatalog;
use App\Cases\Templates\CaseCustomers;
use App\Cases\Templates\CaseTemplate;
use App\Cases\Templates\CaseTemplateCatalog;
use App\Cases\Templates\TemplateCaseOpener;
use App\CivilRegistry\ApplicationIntake;
use App\CivilRegistry\ApplicationKind;
use App\Game\GameClock;
use App\Logistics\ShipmentDispatch;
use App\Logistics\Supply\SupplyRoute;
use App\Logistics\Supply\SupplyRouteCatalog;
use App\Logistics\Templates\ShipmentTemplateCatalog;
use App\Models\Branch;
use App\Models\Employment;
use App\Models\WorkCase;
use App\Models\WorldEvent;
use App\World\Events\ServiceProviders;
use App\World\Events\SubjectPicker;

class FirstTask
{
    private const DUE_TIME = '12:00';

    public function __construct(
        private readonly GameClock $clock,
        private readonly CaseCatalog $scriptedCases,
        private readonly CaseTemplateCatalog $caseTemplates,
        private readonly CaseCustomers $customers,
        private readonly TemplateCaseOpener $caseOpener,
        private readonly SupplyRouteCatalog $routes,
        private readonly ServiceProviders $providers,
        private readonly ShipmentDispatch $dispatch,
        private readonly SubjectPicker $subjects,
        private readonly ApplicationIntake $applications,
    ) {}

    public function ensureFor(Employment $employment): void
    {
        $branch = $employment->branch();
        $company = $branch->company;
        $hasWork = WorkCase::query()->where('employment_id', $employment->id)->open()->exists()
            || $this->scriptedCases->forCompany($company) !== [];

        if ($hasWork) {
            return;
        }

        match (true) {
            $company->offers('freight') => $this->supplyOrder($branch, $employment),
            $company->offers('registration') => $this->application($branch, $employment),
            default => $this->templateCase($branch, $employment),
        };
    }

    private function supplyOrder(Branch $branch, Employment $employment): void
    {
        $city = $branch->city;
        $route = collect($this->routes->routesIn($city))->first();
        $supplier = $route === null ? null : $this->providers->branchOf($city, $route->supplier);
        $recipient = $route === null ? null : $this->providers->branchOf($city, $route->recipient);

        if (! $route instanceof SupplyRoute || $supplier === null || $recipient === null) {
            return;
        }

        $due = $this->clock->today()->addWeekday()->setTimeFromTimeString(self::DUE_TIME);

        $this->dispatch->send($city, $supplier, $recipient, ShipmentTemplateCatalog::RESTOCK_DELIVERY, [
            'order_key' => "onboarding|{$employment->id}",
            'contents' => $route->contents,
            'size' => $route->size,
            'due_date' => $due->toDateString(),
            'due_slot' => $due->format('H:i'),
        ], $employment->position);
    }

    private function application(Branch $branch, Employment $employment): void
    {
        $person = $this->subjects->pick($branch->city, $branch, "onboarding|{$employment->id}", false);

        if ($person === null) {
            return;
        }

        $event = WorldEvent::create([
            'city_id' => $branch->city_id,
            'person_id' => $person->id,
            'key' => "onboarding|{$employment->id}",
            'type' => 'moving',
            'occurred_at' => now(),
        ]);

        $this->applications->receive($branch, $event, ApplicationKind::Move, $employment->position);
    }

    private function templateCase(Branch $branch, Employment $employment): void
    {
        $responsibilities = $employment->position->responsibilities;
        $template = collect($this->caseTemplates->forCompany($branch->company))
            ->first(fn (CaseTemplate $template): bool => in_array($template->responsibility, $responsibilities, true));
        $customer = $this->customers->nextWithoutOpenCase($branch);

        if ($template !== null && $customer !== null) {
            $this->caseOpener->open($branch, $template, $customer, null, $employment->position);
        }
    }
}
