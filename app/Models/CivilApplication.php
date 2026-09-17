<?php

namespace App\Models;

use App\CivilRegistry\ApplicationDecision;
use App\CivilRegistry\ApplicationKind;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $branch_id
 * @property ApplicationKind $kind
 * @property int $applicant_id
 * @property int|null $partner_id
 * @property string $claimed_district
 * @property string $claimed_street
 * @property string|null $claimed_partner_district
 * @property string|null $claimed_partner_street
 * @property string|null $new_district
 * @property string|null $new_street
 * @property CarbonImmutable $moved_on
 * @property ApplicationDecision|null $decision
 * @property bool|null $matched_registry
 * @property CarbonImmutable|null $decided_at
 * @property int|null $decided_by_employment_id
 * @property int|null $decided_by_position_id
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Branch $branch
 * @property-read Person $applicant
 * @property-read Person|null $partner
 * @property-read WorkCase|null $workCase
 * @property-read Employment|null $decidedBy
 * @property-read Position|null $decidedByPosition
 */
#[Fillable(['branch_id', 'kind', 'applicant_id', 'partner_id', 'claimed_district', 'claimed_street', 'claimed_partner_district', 'claimed_partner_street', 'new_district', 'new_street', 'moved_on', 'decision', 'matched_registry', 'decided_at', 'decided_by_employment_id', 'decided_by_position_id'])]
class CivilApplication extends Model
{
    public function isDecided(): bool
    {
        return $this->decision !== null;
    }

    public function isDecidedCorrectly(): bool
    {
        return $this->matched_registry !== null
            && $this->decision === ($this->matched_registry ? ApplicationDecision::Approved : ApplicationDecision::Rejected);
    }

    /**
     * @return BelongsTo<Branch, $this>
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @return BelongsTo<Person, $this>
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'applicant_id');
    }

    /**
     * @return BelongsTo<Person, $this>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'partner_id');
    }

    /**
     * @return HasOne<WorkCase, $this>
     */
    public function workCase(): HasOne
    {
        return $this->hasOne(WorkCase::class);
    }

    /**
     * @return BelongsTo<Employment, $this>
     */
    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(Employment::class, 'decided_by_employment_id');
    }

    /**
     * @return BelongsTo<Position, $this>
     */
    public function decidedByPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'decided_by_position_id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kind' => ApplicationKind::class,
            'decision' => ApplicationDecision::class,
            'moved_on' => 'immutable_date',
            'matched_registry' => 'boolean',
            'decided_at' => 'datetime',
        ];
    }
}
