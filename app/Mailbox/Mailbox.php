<?php

namespace App\Mailbox;

use App\Models\Email;
use App\Models\User;

class Mailbox
{
    public function deliver(User $player, EmailDraft $draft): Email
    {
        return $player->emails()->create([
            'sender_name' => $draft->senderName,
            'sender_address' => $draft->senderAddress,
            'subject' => $draft->subject,
            'body' => $draft->body,
            'received_at' => now(),
        ]);
    }

    public function markRead(Email $email): void
    {
        if ($email->read_at === null) {
            $email->update(['read_at' => now()]);
        }
    }
}
