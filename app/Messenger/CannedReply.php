<?php

namespace App\Messenger;

readonly class CannedReply
{
    public function __construct(
        public string $slug,
        public string $text,
        public string $answer,
    ) {}

    /**
     * @param  array{slug: string, text: string, answer: string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data['slug'], $data['text'], $data['answer']);
    }

    /**
     * @return array{slug: string, text: string, answer: string}
     */
    public function toArray(): array
    {
        return ['slug' => $this->slug, 'text' => $this->text, 'answer' => $this->answer];
    }
}
