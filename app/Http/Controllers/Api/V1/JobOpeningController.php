<?php

namespace App\Http\Controllers\Api\V1;

use App\Careers\JobBoard;
use App\Http\Resources\JobOpeningResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JobOpeningController extends ApiController
{
    public function index(Request $request, JobBoard $board): AnonymousResourceCollection
    {
        return JobOpeningResource::collection($board->openingsFor($this->player($request)));
    }
}
