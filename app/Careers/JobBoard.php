<?php

namespace App\Careers;

use App\Models\Company;
use App\Models\JobOpening;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class JobBoard
{
    /**
     * @return Collection<int, JobOpening>
     */
    public function openingsFor(User $player): Collection
    {
        return JobOpening::query()
            ->with('company')
            ->where('is_open', true)
            ->whereRelation('company', 'locale', $player->locale)
            ->orderBy(Company::query()->select('name')->whereColumn('companies.id', 'job_openings.company_id'))
            ->orderBy('title')
            ->get();
    }
}
