<?php

namespace App\Mailbox;

readonly class OutgoingEmail
{
    public function __construct(
        public EmailRecipient $recipient,
        public string $subject,
        public string $body,
        public EmailAction $action,
    ) {}
}
