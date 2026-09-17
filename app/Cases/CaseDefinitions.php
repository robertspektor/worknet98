<?php

namespace App\Cases;

use App\Cases\Templates\CaseTemplateCatalog;
use App\Cases\Templates\TemplateCaseBuilder;
use App\Models\WorkCase;

class CaseDefinitions
{
    public function __construct(
        private readonly CaseCatalog $scripted,
        private readonly CaseTemplateCatalog $templates,
        private readonly TemplateCaseBuilder $builder,
    ) {}

    public function for(WorkCase $workCase): ?CaseDefinition
    {
        $company = $workCase->branch->company;

        if ($workCase->kind === WorkCaseKind::Scripted) {
            return $this->scripted->find($company, $workCase->case_slug);
        }

        $template = $this->templates->find($company, $workCase->case_slug);

        return $template === null ? null : $this->builder->build($template, $workCase->customer);
    }
}
