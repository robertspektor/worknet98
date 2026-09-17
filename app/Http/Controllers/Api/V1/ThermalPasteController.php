<?php

namespace App\Http\Controllers\Api\V1;

use App\Hardware\DeskParts;
use App\Hardware\HomeComputer;
use App\Hardware\ThermalPasteApplier;
use App\Http\Resources\HomeComputerResource;
use Illuminate\Http\Request;

class ThermalPasteController extends ApiController
{
    public function store(
        Request $request,
        ThermalPasteApplier $applier,
        HomeComputer $homeComputer,
        DeskParts $deskParts,
    ): HomeComputerResource {
        $player = $this->player($request);
        $applier->apply($player);

        return new HomeComputerResource($homeComputer->cpuOf($player), $deskParts->of($player));
    }
}
