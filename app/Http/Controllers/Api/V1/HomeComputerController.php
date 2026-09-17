<?php

namespace App\Http\Controllers\Api\V1;

use App\Hardware\DeskParts;
use App\Hardware\HomeComputer;
use App\Http\Resources\HomeComputerResource;
use Illuminate\Http\Request;

class HomeComputerController extends ApiController
{
    public function show(Request $request, HomeComputer $homeComputer, DeskParts $deskParts): HomeComputerResource
    {
        $player = $this->player($request);

        return new HomeComputerResource($homeComputer->cpuOf($player), $deskParts->of($player));
    }
}
