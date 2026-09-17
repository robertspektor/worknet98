<?php

namespace App\Http\Controllers\Api\V1;

use App\Desk\DeskArrangement;
use App\Http\Requests\PlaceDeskItemRequest;
use App\Http\Resources\DeskPlacementResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DeskPlacementController extends ApiController
{
    public function index(Request $request, DeskArrangement $arrangement): AnonymousResourceCollection
    {
        return DeskPlacementResource::collection($arrangement->of($this->player($request)));
    }

    public function store(PlaceDeskItemRequest $request, DeskArrangement $arrangement): DeskPlacementResource
    {
        $placement = $arrangement->place($this->player($request), $request->item(), $request->x(), $request->y());

        return new DeskPlacementResource($placement);
    }
}
