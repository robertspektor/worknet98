<?php

namespace App\Cases\Templates;

use App\Models\Company;
use Illuminate\Support\Facades\File;

class CaseTemplateCatalog
{
    private const CONTENT_DIRECTORY = 'content/case_templates';

    /**
     * @return list<CaseTemplate>
     */
    public function forCompany(Company $company): array
    {
        $path = database_path(self::CONTENT_DIRECTORY."/{$company->slug}.json");

        if (! File::exists($path)) {
            return [];
        }

        /** @var list<array<string, mixed>> $templates */
        $templates = File::json($path, JSON_THROW_ON_ERROR);

        return array_map($this->template(...), $templates);
    }

    public function find(Company $company, string $slug): ?CaseTemplate
    {
        return collect($this->forCompany($company))->first(fn (CaseTemplate $template): bool => $template->slug === $slug);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function template(array $data): CaseTemplate
    {
        /** @var array{subject: string, body: string} $requestMail */
        $requestMail = $data['request_mail'];
        /** @var array<string, string> $availabilityNotes */
        $availabilityNotes = $data['availability_notes'];
        /** @var array<string, array{met: string, missed: string}> $outcomeFeedback */
        $outcomeFeedback = $data['outcome_feedback'];
        /** @var array{subject: string, intro: string, outro: string} $feedbackMail */
        $feedbackMail = $data['feedback_mail'];
        /** @var array{subject: string, body: string} $reminderMail */
        $reminderMail = $data['reminder_mail'];

        return new CaseTemplate(
            slug: (string) $data['slug'],
            responsibility: (string) $data['responsibility'],
            skill: (string) $data['skill'],
            urgentWithinWorkDays: (int) $data['urgent_within_work_days'],
            requestMail: $requestMail,
            availabilityNotes: $availabilityNotes,
            outcomeFeedback: $outcomeFeedback,
            feedbackMail: $feedbackMail,
            reminderMail: $reminderMail,
        );
    }
}
