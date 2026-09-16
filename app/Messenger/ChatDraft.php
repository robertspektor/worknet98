<?php

namespace App\Messenger;

readonly class ChatDraft
{
    /**
     * @param  list<CannedReply>  $replies
     */
    public function __construct(
        public string $contactName,
        public string $body,
        public array $replies = [],
        public int $delaySeconds = 0,
        public ?int $workCaseId = null,
        public ?string $messageSlug = null,
    ) {}
}
