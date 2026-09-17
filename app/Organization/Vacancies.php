<?php

namespace App\Organization;

use App\Models\JobOpening;
use App\Models\Position;
use Illuminate\Database\Eloquent\Builder;

class Vacancies
{
    public function existFor(JobOpening $opening): bool
    {
        return $this->vacantPositionsFor($opening)->exists();
    }

    public function claimFor(JobOpening $opening): ?Position
    {
        return $this->vacantPositionsFor($opening)->lockForUpdate()->first();
    }

    /**
     * @return Builder<Position>
     */
    private function vacantPositionsFor(JobOpening $opening): Builder
    {
        return Position::query()->whereBelongsTo($opening)->vacant()->orderBy('id');
    }
}
