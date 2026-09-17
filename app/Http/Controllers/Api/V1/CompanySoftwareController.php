<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\CompanySoftwareResource;
use Illuminate\Http\Request;

class CompanySoftwareController extends ApiController
{
    public function show(Request $request): CompanySoftwareResource
    {
        return new CompanySoftwareResource($this->employment($request)->branch());
    }
}
