<?php

namespace App\Messenger;

use App\Game\ActionRefused;
use App\Models\ChatMessage;
use App\Models\User;
use App\Work\DutyCheck;
use Illuminate\Support\Facades\DB;

class ChatReplier
{
    private const ANSWER_DELAY_SECONDS = 5;

    public function __construct(
        private readonly DutyCheck $dutyCheck,
        private readonly Messenger $messenger,
    ) {}

    public function reply(User $player, ChatMessage $message, string $replySlug): ChatMessage
    {
        $this->dutyCheck->employmentOnDuty($player);

        return DB::transaction(function () use ($message, $replySlug): ChatMessage {
            $locked = ChatMessage::query()->whereKey($message->id)->lockForUpdate()->firstOrFail();
            $reply = $this->chosenReply($locked, $replySlug);
            $locked->update(['replied_at' => now()]);

            $sent = $this->messenger->sendFromPlayer($locked->employment, $locked->contact_name, $reply->text);
            $this->messenger->deliver($locked->employment, new ChatDraft(
                contactName: $locked->contact_name,
                body: $reply->answer,
                delaySeconds: self::ANSWER_DELAY_SECONDS,
            ));

            return $sent;
        });
    }

    private function chosenReply(ChatMessage $message, string $replySlug): CannedReply
    {
        if ($message->replied_at !== null) {
            throw new ActionRefused(ChatRefusal::AlreadyReplied);
        }

        return $message->cannedReply($replySlug) ?? throw new ActionRefused(ChatRefusal::UnknownReply);
    }
}
