<?php

namespace App\Mailbox;

use App\Models\Customer;

readonly class OutgoingEmail
{
    public function __construct(
        public Customer $recipient,
        public string $subject,
        public string $body,
        public EmailAction $action,
    ) {}
}
