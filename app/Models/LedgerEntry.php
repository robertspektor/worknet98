<?php

namespace App\Models;

use App\Work\LedgerReason;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $amount
 * @property LedgerReason $reason
 * @property int|null $shift_id
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
#[Fillable(['user_id', 'amount', 'reason', 'shift_id'])]
class LedgerEntry extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'reason' => LedgerReason::class,
        ];
    }
}
