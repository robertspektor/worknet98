<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\PlayerResource;
use Illuminate\Http\Request;

class PlayerController extends ApiController
{
    public function show(Request $request): PlayerResource
    {
        return new PlayerResource($this->player($request));
    }
}
