<?php

namespace App\Http\Controllers\Api\V1;

use App\Hardware\HomeComputer;
use App\Http\Resources\HomeComputerResource;
use Illuminate\Http\Request;

class HomeComputerController extends ApiController
{
    public function show(Request $request, HomeComputer $homeComputer): HomeComputerResource
    {
        return new HomeComputerResource($homeComputer->cpuOf($this->player($request)));
    }
}
