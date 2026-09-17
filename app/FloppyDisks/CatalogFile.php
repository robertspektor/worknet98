<?php

namespace App\FloppyDisks;

readonly class CatalogFile
{
    public function __construct(
        public string $name,
        public DiskFileKind $kind,
        public ?string $contentKey = null,
        public ?string $program = null,
        public ?int $sizeBytes = null,
    ) {}

    /**
     * @param  array{name: string, kind: string, content_key?: string, program?: string, size_bytes?: int}  $entry
     */
    public static function fromArray(array $entry): self
    {
        return new self(
            name: $entry['name'],
            kind: DiskFileKind::from($entry['kind']),
            contentKey: $entry['content_key'] ?? null,
            program: $entry['program'] ?? null,
            sizeBytes: $entry['size_bytes'] ?? null,
        );
    }
}
