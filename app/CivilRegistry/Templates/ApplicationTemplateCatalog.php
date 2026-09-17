<?php

namespace App\CivilRegistry\Templates;

use App\CivilRegistry\ApplicationKind;
use App\Content\CompanyContentFile;
use App\Models\Company;

class ApplicationTemplateCatalog
{
    private const CONTENT_DIRECTORY = 'application_templates';

    public function find(Company $company, string $slug): ?ApplicationTemplate
    {
        return collect($this->forCompany($company))->first(fn (ApplicationTemplate $template): bool => $template->slug === $slug);
    }

    public function forKind(Company $company, ApplicationKind $kind): ?ApplicationTemplate
    {
        return collect($this->forCompany($company))->first(fn (ApplicationTemplate $template): bool => $template->kind === $kind);
    }

    /**
     * @return list<ApplicationTemplate>
     */
    public function forCompany(Company $company): array
    {
        return array_map($this->template(...), CompanyContentFile::entries($company, self::CONTENT_DIRECTORY));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function template(array $data): ApplicationTemplate
    {
        /** @var array{slug: string, kind: string, responsibility: string, date_format: string, request_mail: array{subject: string, body: string}, outcome_feedback: array{approved_valid: string, approved_invalid: string, rejected_valid: string, rejected_invalid: string}, feedback_mail: array{subject: string, intro: string, outro: string}, reminder_mail: array{subject: string, body: string}} $data */
        return new ApplicationTemplate(
            slug: $data['slug'],
            kind: ApplicationKind::from($data['kind']),
            responsibility: $data['responsibility'],
            dateFormat: $data['date_format'],
            requestMail: $data['request_mail'],
            outcomeFeedback: $data['outcome_feedback'],
            feedbackMail: $data['feedback_mail'],
            reminderMail: $data['reminder_mail'],
        );
    }
}
