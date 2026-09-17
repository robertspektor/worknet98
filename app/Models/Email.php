<?php

namespace App\Models;

use App\Mailbox\EmailAction;
use App\Mailbox\EmailFolder;
use Carbon\CarbonImmutable;
use Database\Factories\EmailFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $user_id
 * @property string $sender_name
 * @property string $sender_address
 * @property string $subject
 * @property string $body
 * @property CarbonImmutable $received_at
 * @property CarbonImmutable|null $read_at
 * @property int|null $employment_id
 * @property EmailFolder $folder
 * @property string|null $recipient_name
 * @property string|null $recipient_address
 * @property EmailAction|null $action
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read User $user
 * @property-read PromotionOffer|null $promotionOffer
 */
#[Fillable(['user_id', 'sender_name', 'sender_address', 'subject', 'body', 'received_at', 'read_at', 'employment_id', 'folder', 'recipient_name', 'recipient_address', 'action'])]
class Email extends Model
{
    /** @use HasFactory<EmailFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasOne<PromotionOffer, $this>
     */
    public function promotionOffer(): HasOne
    {
        return $this->hasOne(PromotionOffer::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'received_at' => 'datetime',
            'read_at' => 'datetime',
            'folder' => EmailFolder::class,
            'action' => EmailAction::class,
        ];
    }
}
