<?php

namespace App\Console\Commands;

use App\Cases\Templates\CaseCustomers;
use App\Cases\Templates\CaseTemplateCatalog;
use App\Cases\Templates\TemplateCaseOpener;
use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('cases:open {company : Company slug} {branch : Branch slug} {template : Case template slug} {--customer= : Customer slug, defaults to the next customer without an open case}')]
#[Description('Open a case from a template in a branch and route it to the responsible position')]
class OpenCase extends Command
{
    public function handle(CaseTemplateCatalog $templates, CaseCustomers $customers, TemplateCaseOpener $opener): int
    {
        $branch = Branch::query()
            ->whereRelation('company', 'slug', $this->argument('company'))
            ->where('slug', $this->argument('branch'))
            ->firstOrFail();
        $template = $templates->find($branch->company, (string) $this->argument('template'));
        $customer = $this->customer($branch, $customers);

        if ($template === null || $customer === null || $customers->hasOpenCase($customer)) {
            $this->error('Unknown template, or no customer without an open case.');

            return self::FAILURE;
        }

        $workCase = $opener->open($branch, $template, $customer);
        $this->info("Opened [{$template->slug}] for {$customer->name}, assigned to {$workCase->position->npc_name} ({$workCase->position->slug}).");

        return self::SUCCESS;
    }

    private function customer(Branch $branch, CaseCustomers $customers): ?Customer
    {
        $slug = $this->option('customer');

        return $slug === null
            ? $customers->nextWithoutOpenCase($branch)
            : $branch->customers()->where('slug', $slug)->first();
    }
}
