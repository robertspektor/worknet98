<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $forum_thread_id
 * @property int $author_position_id
 * @property int|null $author_employment_id
 * @property string $body
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read ForumThread $thread
 * @property-read Position $authorPosition
 * @property-read Employment|null $authorEmployment
 */
#[Fillable(['forum_thread_id', 'author_position_id', 'author_employment_id', 'body'])]
class ForumPost extends Model
{
    /**
     * @return BelongsTo<ForumThread, $this>
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(ForumThread::class, 'forum_thread_id');
    }

    /**
     * @return BelongsTo<Position, $this>
     */
    public function authorPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'author_position_id');
    }

    /**
     * @return BelongsTo<Employment, $this>
     */
    public function authorEmployment(): BelongsTo
    {
        return $this->belongsTo(Employment::class, 'author_employment_id');
    }
}
