<?php

namespace App\Models;

use App\Careers\JobApplicationStatus;
use Carbon\CarbonImmutable;
use Database\Factories\JobApplicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $job_opening_id
 * @property string|null $message
 * @property JobApplicationStatus $status
 * @property CarbonImmutable $responds_at
 * @property CarbonImmutable|null $responded_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read User $user
 * @property-read JobOpening $jobOpening
 */
#[Fillable(['user_id', 'job_opening_id', 'message', 'status', 'responds_at', 'responded_at'])]
class JobApplication extends Model
{
    /** @use HasFactory<JobApplicationFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<JobOpening, $this>
     */
    public function jobOpening(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class);
    }

    /**
     * @param  Builder<JobApplication>  $query
     */
    public function scopePending(Builder $query): void
    {
        $query->where('status', JobApplicationStatus::Pending);
    }

    /**
     * @param  Builder<JobApplication>  $query
     */
    public function scopeDueForResponse(Builder $query): void
    {
        $query->pending()->where('responds_at', '<=', now());
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => JobApplicationStatus::class,
            'responds_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }
}
