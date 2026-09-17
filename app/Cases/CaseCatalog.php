<?php

namespace App\Cases;

use App\Cases\Conditions\ConditionFactory;
use App\Content\CompanyContentFile;
use App\Messenger\CannedReply;
use App\Models\Company;

class CaseCatalog
{
    private const CONTENT_DIRECTORY = 'cases';

    public function __construct(private readonly ConditionFactory $conditions) {}

    /**
     * @return list<ScriptedCase>
     */
    public function forCompany(Company $company): array
    {
        return array_map(
            $this->scriptedCase(...),
            CompanyContentFile::entries($company, self::CONTENT_DIRECTORY),
        );
    }

    public function find(Company $company, string $slug): ?CaseDefinition
    {
        return collect($this->forCompany($company))
            ->map(fn (ScriptedCase $scripted): CaseDefinition => $scripted->definition)
            ->first(fn (CaseDefinition $definition): bool => $definition->slug === $slug);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function scriptedCase(array $data): ScriptedCase
    {
        /** @var array{subject: string, body: string}|null $briefingMail */
        $briefingMail = $data['briefing_mail'] ?? null;

        return new ScriptedCase(shift: (int) $data['shift'], definition: $this->definition($data), briefingMail: $briefingMail);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function definition(array $data): CaseDefinition
    {
        /** @var list<array<string, mixed>> $goals */
        $goals = $data['goals'];
        /** @var list<array<string, mixed>> $messages */
        $messages = $data['messages'] ?? [];
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
            requestMail: $requestMail,
            goals: array_map($this->conditions->make(...), $goals),
            messages: array_map($this->message(...), $messages),
            outcomes: array_map($this->outcome(...), $outcomes),
            feedbackMail: $feedbackMail,
            reminderMail: $reminderMail,
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function message(array $data): CaseMessage
    {
        /** @var array<string, mixed>|null $when */
        $when = $data['when'] ?? null;
        /** @var list<array{slug: string, text: string, answer: string}> $replies */
        $replies = $data['replies'] ?? [];

        return new CaseMessage(
            slug: (string) $data['slug'],
            trigger: MessageTrigger::from((string) $data['trigger']),
            delaySeconds: (int) ($data['delay_seconds'] ?? 0),
            sender: (string) $data['sender'],
            when: $when === null ? null : $this->conditions->make($when),
            body: (string) $data['body'],
            replies: array_map(CannedReply::fromArray(...), $replies),
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
