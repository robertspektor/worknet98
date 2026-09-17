<?php

namespace App\Cases;

use App\Messenger\ChatDraft;
use App\Messenger\Messenger;
use App\Models\ChatMessage;
use App\Models\Position;
use App\Models\WorkCase;

class CaseMessenger
{
    public function __construct(
        private readonly CaseCatalog $catalog,
        private readonly CaseStateLoader $states,
        private readonly Messenger $messenger,
    ) {}

    public function sendForOpenCases(int $employmentId, MessageTrigger $trigger): void
    {
        WorkCase::query()
            ->where('employment_id', $employmentId)
            ->where('status', WorkCaseStatus::Open)
            ->get()
            ->each(fn (WorkCase $workCase) => $this->send($workCase, $trigger));
    }

    public function send(WorkCase $workCase, MessageTrigger $trigger): void
    {
        $definition = $this->catalog->find($workCase->employment->company, $workCase->case_slug);
        $sentSlugs = ChatMessage::query()->where('work_case_id', $workCase->id)->pluck('message_slug');
        $state = $this->states->for($workCase);

        foreach ($definition->messages ?? [] as $message) {
            if ($message->trigger === $trigger && ! $sentSlugs->contains($message->slug) && $message->isDueIn($state)) {
                $this->sendFromColleague($workCase, $message);
            }
        }
    }

    private function sendFromColleague(WorkCase $workCase, CaseMessage $message): void
    {
        $sender = $workCase->employment->branch()->positions()->where('slug', $message->sender)->firstOrFail();

        if (! $sender->isHeldByPlayer()) {
            $this->messenger->deliver($workCase->employment, $this->draft($workCase, $message, $sender));
        }
    }

    private function draft(WorkCase $workCase, CaseMessage $message, Position $sender): ChatDraft
    {
        return new ChatDraft(
            contactName: $sender->npc_name,
            body: $message->body,
            replies: $message->replies,
            delaySeconds: $message->delaySeconds,
            workCaseId: $workCase->id,
            messageSlug: $message->slug,
        );
    }
}
