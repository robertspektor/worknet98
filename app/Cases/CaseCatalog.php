<?php

namespace App\Cases;

use App\Cases\Conditions\ConditionFactory;
use App\Models\Company;
use Illuminate\Support\Facades\File;

class CaseCatalog
{
    private const CONTENT_DIRECTORY = 'content/cases';

    public function __construct(private readonly ConditionFactory $conditions) {}

    /**
     * @return list<CaseDefinition>
     */
    public function forCompany(Company $company): array
    {
        $path = database_path(self::CONTENT_DIRECTORY."/{$company->slug}.json");

        if (! File::exists($path)) {
            return [];
        }

        /** @var list<array<string, mixed>> $definitions */
        $definitions = File::json($path, JSON_THROW_ON_ERROR);

        return array_map($this->definition(...), $definitions);
    }

    public function find(Company $company, string $slug): ?CaseDefinition
    {
        return collect($this->forCompany($company))->first(fn (CaseDefinition $definition): bool => $definition->slug === $slug);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function definition(array $data): CaseDefinition
    {
        /** @var list<array<string, mixed>> $goals */
        $goals = $data['goals'];
        /** @var list<array<string, mixed>> $outcomes */
        $outcomes = $data['outcomes'];
        /** @var array{customer: string, subject: string, body: string} $requestMail */
        $requestMail = $data['request_mail'];
        /** @var array{subject: string, intro: string, outro: string} $feedbackMail */
        $feedbackMail = $data['feedback_mail'];
        /** @var array{subject: string, body: string} $reminderMail */
        $reminderMail = $data['reminder_mail'];

        return new CaseDefinition(
            slug: (string) $data['slug'],
            shift: (int) $data['shift'],
            requestMail: $requestMail,
            goals: array_map($this->conditions->make(...), $goals),
            outcomes: array_map($this->outcome(...), $outcomes),
            feedbackMail: $feedbackMail,
            reminderMail: $reminderMail,
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function outcome(array $data): Outcome
    {
        /** @var array<string, mixed> $when */
        $when = $data['when'];
        /** @var array<string, int> $effects */
        $effects = $data['effects'];
        /** @var array<string, int> $otherwise */
        $otherwise = $data['otherwise'];

        return new Outcome(
            when: $this->conditions->make($when),
            effects: $effects,
            otherwiseEffects: $otherwise,
            feedback: (string) $data['feedback'],
            otherwiseFeedback: (string) $data['otherwise_feedback'],
        );
    }
}
