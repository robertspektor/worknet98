<?php

namespace App\Http\Controllers\Api\V1;

use App\Hardware\CpuSwapper;
use App\Hardware\DeskParts;
use App\Hardware\HomeComputer;
use App\Http\Requests\InstallDeskPartRequest;
use App\Http\Resources\HomeComputerResource;
use App\Models\PlayerHardwarePart;

class DeskPartInstallationController extends ApiController
{
    public function store(
        InstallDeskPartRequest $request,
        PlayerHardwarePart $deskPart,
        CpuSwapper $swapper,
        HomeComputer $homeComputer,
        DeskParts $deskParts,
    ): HomeComputerResource {
        $player = $this->player($request);
        $swapper->swap($player, $deskPart, $request->thermalPasteApplied());

        return new HomeComputerResource($homeComputer->cpuOf($player), $deskParts->of($player));
    }
}
