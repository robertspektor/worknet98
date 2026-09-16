<?php

namespace App\Messenger;

use App\Models\ChatMessage;
use App\Models\Employment;

class Messenger
{
    public function deliver(Employment $employment, ChatDraft $draft): ChatMessage
    {
        return ChatMessage::create([
            'employment_id' => $employment->id,
            'work_case_id' => $draft->workCaseId,
            'message_slug' => $draft->messageSlug,
            'contact_name' => $draft->contactName,
            'is_from_player' => false,
            'body' => $draft->body,
            'replies' => $draft->replies === [] ? null : array_map(fn (CannedReply $reply): array => $reply->toArray(), $draft->replies),
            'sent_at' => now()->addSeconds($draft->delaySeconds),
        ]);
    }

    public function sendFromPlayer(Employment $employment, string $contactName, string $body): ChatMessage
    {
        return ChatMessage::create([
            'employment_id' => $employment->id,
            'contact_name' => $contactName,
            'is_from_player' => true,
            'body' => $body,
            'sent_at' => now(),
            'read_at' => now(),
        ]);
    }
}
