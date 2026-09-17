<?php

namespace App\Logistics\Templates;

use App\Content\CompanyContentFile;
use App\Models\Company;

class ShipmentTemplateCatalog
{
    public const SPARE_PART_DELIVERY = 'spare-part-delivery';

    private const CONTENT_DIRECTORY = 'shipment_templates';

    public function find(Company $company, string $slug): ?ShipmentTemplate
    {
        return collect(CompanyContentFile::entries($company, self::CONTENT_DIRECTORY))
            ->where('slug', $slug)
            ->map($this->template(...))
            ->first();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function template(array $data): ShipmentTemplate
    {
        /** @var array{slug: string, responsibility: string, due_format: string, size_labels: array<string, string>, request_mail: array{subject: string, body: string}, outcome_feedback: array<string, array{met: string, missed: string}>, feedback_mail: array{subject: string, intro: string, outro: string}, reminder_mail: array{subject: string, body: string}, complaint_mail: array{subject: string, body: string}} $data */
        return new ShipmentTemplate(
            slug: $data['slug'],
            responsibility: $data['responsibility'],
            dueFormat: $data['due_format'],
            sizeLabels: $data['size_labels'],
            requestMail: $data['request_mail'],
            outcomeFeedback: $data['outcome_feedback'],
            feedbackMail: $data['feedback_mail'],
            reminderMail: $data['reminder_mail'],
            complaintMail: $data['complaint_mail'],
        );
    }
}
