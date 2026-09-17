<?php

namespace App\Http\Resources;

use App\Models\CivilApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CivilApplication
 */
class CivilApplicationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kind' => $this->kind->value,
            'applicant' => $this->applicant->name,
            'claimed_street' => $this->claimed_street,
            'claimed_district' => $this->claimed_district,
            'partner' => $this->partner?->name,
            'claimed_partner_street' => $this->claimed_partner_street,
            'claimed_partner_district' => $this->claimed_partner_district,
            'detail' => $this->detail,
            'new_street' => $this->new_street,
            'new_district' => $this->new_district,
            'moved_on' => $this->moved_on->toDateString(),
            'decision' => $this->decision?->value,
            'is_own' => $this->isAssignedTo($request->user()),
            'decided_by' => $this->decidedBy?->position->title,
            'decided_by_npc' => $this->decision !== null && $this->decided_by_employment_id === null ? $this->decidedByPosition?->person->name : null,
        ];
    }

    private function isAssignedTo(mixed $player): bool
    {
        return $player instanceof User
            && $this->workCase?->employment_id !== null
            && $this->workCase->employment_id === $player->employment?->id;
    }
}
