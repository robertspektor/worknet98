<?php

namespace App\Models;

use App\Messenger\CannedReply;
use Carbon\CarbonImmutable;
use Database\Factories\ChatMessageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $employment_id
 * @property int|null $work_case_id
 * @property string|null $message_slug
 * @property string $contact_name
 * @property bool $is_from_player
 * @property string $body
 * @property list<array{slug: string, text: string, answer: string}>|null $replies
 * @property CarbonImmutable|null $replied_at
 * @property CarbonImmutable $sent_at
 * @property CarbonImmutable|null $read_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Employment $employment
 */
#[Fillable(['employment_id', 'work_case_id', 'message_slug', 'contact_name', 'is_from_player', 'body', 'replies', 'replied_at', 'sent_at', 'read_at'])]
class ChatMessage extends Model
{
    /** @use HasFactory<ChatMessageFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Employment, $this>
     */
    public function employment(): BelongsTo
    {
        return $this->belongsTo(Employment::class);
    }

    /**
     * @return list<CannedReply>
     */
    public function cannedReplies(): array
    {
        return array_map(CannedReply::fromArray(...), $this->replies ?? []);
    }

    public function cannedReply(string $slug): ?CannedReply
    {
        return collect($this->cannedReplies())->first(fn (CannedReply $reply): bool => $reply->slug === $slug);
    }

    public function awaitsReply(): bool
    {
        return $this->replied_at === null && $this->cannedReplies() !== [];
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_from_player' => 'boolean',
            'replies' => 'array',
            'replied_at' => 'datetime',
            'sent_at' => 'datetime',
            'read_at' => 'datetime',
        ];
    }
}
