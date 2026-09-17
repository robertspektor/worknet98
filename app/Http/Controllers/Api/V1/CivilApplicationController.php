<?php

namespace App\Http\Controllers\Api\V1;

use App\CivilRegistry\ApplicationLog;
use App\Http\Resources\CivilApplicationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CivilApplicationController extends ApiController
{
    public function index(Request $request, ApplicationLog $log): AnonymousResourceCollection
    {
        return CivilApplicationResource::collection($log->recentOf($this->employment($request)->branch()));
    }
}
