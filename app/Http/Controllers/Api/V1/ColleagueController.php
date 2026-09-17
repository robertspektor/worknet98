<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ColleagueResource;
use App\Mailbox\Colleagues;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ColleagueController extends ApiController
{
    public function index(Request $request, Colleagues $colleagues): AnonymousResourceCollection
    {
        return ColleagueResource::collection($colleagues->of($this->employment($request)));
    }
}
