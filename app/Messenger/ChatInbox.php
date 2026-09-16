<?php

namespace App\Messenger;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ChatInbox
{
    /**
     * @return Collection<int, ChatMessage>
     */
    public function messagesOf(User $player): Collection
    {
        return $this->arrived($player)->orderBy('sent_at')->orderBy('id')->get();
    }

    public function markAllRead(User $player): void
    {
        $this->arrived($player)->whereNull('read_at')->update(['read_at' => now()]);
    }

    /**
     * @return Builder<ChatMessage>
     */
    private function arrived(User $player): Builder
    {
        return ChatMessage::query()
            ->where('employment_id', $player->employment?->id)
            ->where('sent_at', '<=', now());
    }
}
