<?php

namespace App\Http\Controllers\Api\V1;

use App\CivilRegistry\ApplicationDecider;
use App\Http\Requests\DecideCivilApplicationRequest;
use App\Http\Resources\CivilApplicationResource;
use App\Models\CivilApplication;

class CivilApplicationDecisionController extends ApiController
{
    public function store(DecideCivilApplicationRequest $request, CivilApplication $civilApplication, ApplicationDecider $decider): CivilApplicationResource
    {
        $decided = $decider->decide($this->player($request), $civilApplication, $request->decision());

        return new CivilApplicationResource($decided->load(['applicant', 'partner', 'workCase', 'decidedBy.position', 'decidedByPosition.person']));
    }
}
