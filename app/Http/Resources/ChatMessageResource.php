<?php

namespace App\Http\Resources;

use App\Messenger\CannedReply;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ChatMessage
 */
class ChatMessageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'contact_name' => $this->contact_name,
            'is_from_player' => $this->is_from_player,
            'body' => $this->body,
            'sent_at' => $this->sent_at->toIso8601String(),
            'is_read' => $this->read_at !== null,
            'replies' => $this->awaitsReply()
                ? array_map(fn (CannedReply $reply): array => ['slug' => $reply->slug, 'text' => $reply->text], $this->cannedReplies())
                : [],
        ];
    }
}
