<?php

namespace App\Mailbox;

use App\Mailbox\Events\EmailSent;
use App\Models\Email;
use App\Models\User;
use App\Work\DutyCheck;

class WorkEmailSender
{
    public function __construct(private readonly DutyCheck $dutyCheck) {}

    public function send(User $player, OutgoingEmail $outgoing): Email
    {
        $employment = $this->dutyCheck->employmentOnDuty($player);

        $email = $player->emails()->create([
            'employment_id' => $employment->id,
            'folder' => EmailFolder::Sent,
            'sender_name' => $employment->company->name,
            'sender_address' => $employment->branch()->office_address,
            'recipient_name' => $outgoing->recipient->name,
            'recipient_address' => $outgoing->recipient->email_address,
            'subject' => $outgoing->subject,
            'body' => $outgoing->body,
            'action' => $outgoing->action,
            'received_at' => now(),
            'read_at' => now(),
        ]);

        EmailSent::dispatch($email);

        return $email;
    }
}
