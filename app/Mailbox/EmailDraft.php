<?php

namespace App\Mailbox;

readonly class EmailDraft
{
    public function __construct(
        public string $senderName,
        public string $senderAddress,
        public string $subject,
        public string $body,
    ) {}
}
